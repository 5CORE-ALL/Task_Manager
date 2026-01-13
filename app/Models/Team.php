<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkSpace;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_leader_id', // Keeping DB column name for compatibility
        'workspace_id',
        'description',
    ];

    /**
     * Get the team creator (user)
     */
    public function teamCreator()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    /**
     * Alias for backward compatibility
     */
    public function teamLeader()
    {
        return $this->teamCreator();
    }

    /**
     * Get all team members
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'member_id')
                    ->withTimestamps();
    }

    /**
     * Get workspace
     */
    public function workspace()
    {
        return $this->belongsTo(WorkSpace::class, 'workspace_id');
    }

    /**
     * Check if a user is a team creator of any team
     */
    public static function isTeamCreator($userId)
    {
        return self::where('team_leader_id', $userId)->exists();
    }

    /**
     * Alias for backward compatibility
     */
    public static function isTeamLeader($userId)
    {
        return self::isTeamCreator($userId);
    }

    /**
     * Get all teams where user is a team creator
     */
    public static function getTeamsByCreator($userId)
    {
        return self::where('team_leader_id', $userId)->get();
    }

    /**
     * Alias for backward compatibility
     */
    public static function getTeamsByLeader($userId)
    {
        return self::getTeamsByCreator($userId);
    }

    /**
     * Get all teams where user is a member
     */
    public static function getTeamsByMember($userId)
    {
        return self::whereHas('members', function($query) use ($userId) {
            $query->where('member_id', $userId);
        })->get();
    }

    /**
     * Get all team member IDs for teams where user is a creator
     */
    public static function getTeamMemberIdsByCreator($userId)
    {
        $teamIds = self::where('team_leader_id', $userId)->pluck('id');
        return \DB::table('team_members')
            ->whereIn('team_id', $teamIds)
            ->pluck('member_id')
            ->toArray();
    }

    /**
     * Alias for backward compatibility
     */
    public static function getTeamMemberIdsByLeader($userId)
    {
        return self::getTeamMemberIdsByCreator($userId);
    }
}
