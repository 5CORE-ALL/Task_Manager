<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index()
    {
        $currentWorkspace = getActiveWorkSpace();
        $teams = Team::with(['teamCreator', 'members'])
                    ->where('workspace_id', $currentWorkspace)
                    ->get();

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        $currentWorkspace = getActiveWorkSpace();
        $employees = User::where('workspace_id', $currentWorkspace)
                        ->where('type', '!=', 'super admin')
                        ->where('type', '!=', 'company')
                        ->get();

        return view('teams.create', compact('employees'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $currentWorkspace = getActiveWorkSpace();

        // Set the team creator to the current authenticated user
        $team = Team::create([
            'name' => $request->name,
            'team_leader_id' => Auth::id(), // Team creator is always the user who creates it
            'workspace_id' => $currentWorkspace,
            'description' => $request->description,
        ]);

        // Attach team members
        if ($request->has('members') && is_array($request->members)) {
            $team->members()->sync($request->members);
        }

        return redirect()->route('teams.index')
                        ->with('success', 'Team created successfully.');
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Team $team)
    {
        // Check if user is the team creator
        if ($team->team_leader_id !== Auth::id()) {
            abort(403, 'Only the team creator can edit this team.');
        }

        $currentWorkspace = getActiveWorkSpace();
        $employees = User::where('workspace_id', $currentWorkspace)
                        ->where('type', '!=', 'super admin')
                        ->where('type', '!=', 'company')
                        ->get();

        $team->load('members');

        return view('teams.edit', compact('team', 'employees'));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team)
    {
        // Check if user is the team creator
        if ($team->team_leader_id !== Auth::id()) {
            abort(403, 'Only the team creator can update this team.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        // Only allow team creator to change themselves (team_leader_id can only be changed by creator)
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Only update team_leader_id if it's provided and user is the creator
        if ($request->has('team_leader_id') && $request->team_leader_id != $team->team_leader_id) {
            // Only the current creator can change the creator
            if ($team->team_leader_id === Auth::id()) {
                $updateData['team_leader_id'] = $request->team_leader_id;
            }
        }

        $team->update($updateData);

        // Update team members
        if ($request->has('members') && is_array($request->members)) {
            $team->members()->sync($request->members);
        } else {
            $team->members()->detach();
        }

        return redirect()->route('teams.index')
                        ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified team.
     */
    public function destroy(Team $team)
    {
        // Only the team creator can delete the team
        if ($team->team_leader_id !== Auth::id()) {
            abort(403, 'Only the team creator can delete this team.');
        }

        $team->delete();

        return redirect()->route('teams.index')
                        ->with('success', 'Team deleted successfully.');
    }
}
