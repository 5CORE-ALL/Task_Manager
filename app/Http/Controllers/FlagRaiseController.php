<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FlagRaise;

class FlagRaiseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'team_member_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'flag_type' => 'required|in:red,green',
        ]);

        $flag = new FlagRaise();
        $flag->given_by = Auth::id();
        $flag->team_member_id = $request->input('team_member_id');
        $flag->description = $request->input('description');
        $flag->flag_type = $request->input('flag_type');
        $flag->save();

        return response()->json(['message' => 'Flag raised successfully!']);
    }

    /**
     * Remove the specified flag from storage (hard delete).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->email, ['president@5core.com', 'tech-support@5core.com'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $flag = \App\Models\FlagRaise::findOrFail($id);
        $flag->delete();
        return response()->json(['message' => 'Flag deleted successfully']);
    }

    /**
     * Show the flag history: president sees all, others see their own flags.
     */
    public function history(Request $request)
    {
        $user = \Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }
        if (in_array($user->email, ['president@5core.com', 'tech-support@5core.com'])) {
            // See all flags
            $flags = \App\Models\FlagRaise::with(['givenBy', 'teamMember'])->orderBy('created_at', 'desc')->get();
        } else {
            // See only own flags (given_by = user id or team_member_id = user id)
            $flags = \App\Models\FlagRaise::with(['givenBy', 'teamMember'])
                ->where(function($q) use ($user) {
                    $q->where('given_by', $user->id)
                      ->orWhere('team_member_id', $user->id);
                })
                ->orderBy('created_at', 'desc')->get();
        }
        return view('flag-raise.history', compact('flags'));
    }
}
