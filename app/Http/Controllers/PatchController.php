<?php

namespace App\Http\Controllers;

use App\Models\TeamloggerTime;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class PatchController extends Controller
{
    public function addTeamloggerTime($filter)
    {
        try {
            // Get all users with emails
            $users = User::whereNotNull('email')->get();

            // ------------------- Determine Date Range -------------------
            if ($filter == 'last_30_days') {
                $period = CarbonPeriod::create(
                    now()->subDays(30)->startOfDay(),
                    now()->subDay()->startOfDay()
                );
            } else {
                // Default → only yesterday's data
                $period = CarbonPeriod::create(
                  now()->subDay()->startOfDay(),
                  now()->subDay()->startOfDay()
                );
            }

            // ------------------- Loop Dates -------------------
            foreach ($period as $date) {

                // Build TeamLogger API time range for the date
                $startTime = $date->copy()->setTime(12, 0, 0)->timestamp * 1000;
                $endTime   = $date->copy()->addDay()->setTime(11, 59, 59)->timestamp * 1000;

                // ------------------- Fetch API once per day -------------------
                $curl = curl_init();
                $apiUrl = "https://api2.teamlogger.com/api/employee_summary_report?startTime={$startTime}&endTime={$endTime}";

                curl_setopt_array($curl, [
                    CURLOPT_URL => $apiUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                        'Content-Type: application/json'
                    ],
                ]);

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpCode !== 200 || !$response) {
                    \Log::error("TeamLogger API failed for date {$date->toDateString()}");
                    continue;
                }

                // Decode API response
                $data = json_decode($response, true);
                if (!is_array($data)) {
                    \Log::error("Invalid TL API response for {$date->toDateString()}");
                    continue;
                }

                // ------------------- Build email → record map from API -------------------
                $records = [];

                foreach ($data as $rec) {
                    if (!isset($rec['email'])) continue;

                    $email = strtolower(trim($rec['email']));
                    $records[$email] = [
                        'total' => $rec['totalHours'] ?? 0,
                        'idle'  => $rec['idleHours'] ?? 0
                    ];
                }

                // ------------------- Save Data for Every User -------------------
                foreach ($users as $u) {

                    $email = strtolower(trim($u->email));

                    $totalHours = $records[$email]['total'] ?? 0;
                    $idleHours  = $records[$email]['idle']  ?? 0;
                    $activeHours = max(0, $totalHours - $idleHours);

                    TeamloggerTime::updateOrCreate(
                        [
                            'email' => $email,
                            'date'  => $date->toDateString(),
                        ],
                        [
                            'activeHours' => round($activeHours, 2),
                            'total_hours' => round($totalHours, 2),
                            'idle_hours'  => round($idleHours, 2),
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => '30-day daily TeamLogger data saved successfully'
            ]);

        } catch (\Exception $e) {

            \Log::error("TeamLogger Patch Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
