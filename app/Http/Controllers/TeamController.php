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
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
        }

        $currentWorkspace = getActiveWorkSpace();
        $teams = Team::with(['teamLeader', 'members'])
                    ->where('workspace_id', $currentWorkspace)
                    ->get();

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
        }

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
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'team_leader_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $currentWorkspace = getActiveWorkSpace();

        $team = Team::create([
            'name' => $request->name,
            'team_leader_id' => $request->team_leader_id,
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
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
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
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'team_leader_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $request->name,
            'team_leader_id' => $request->team_leader_id,
            'description' => $request->description,
        ]);

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
        // Only president@5core.com can manage teams
        if (Auth::user()->email !== 'president@5core.com') {
            abort(403, 'You do not have permission to manage teams.');
        }

        $team->delete();

        return redirect()->route('teams.index')
                        ->with('success', 'Team deleted successfully.');
    }
}
