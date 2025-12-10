<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GupshupController extends Controller
{
    public function incoming(Request $request)
    {
        $payload = $request->all();

        $senderPhone = $payload['payload']['sender']['phone'] ?? null;

        if ($senderPhone) {
            $normalizedPhone = substr($senderPhone, -10);

            $user = User::where('mobile_no', 'like', '%' . $normalizedPhone)->first();

            if ($user) {
                $user->last_whatsapp_reply_at = now();
                $user->save();
                
                Log::info("User matched and updated: " . $user->name);
            }
        } 

        return response()->json(['status' => 'received']);
    }

}
