<?php

namespace App\Policies;

use App\Helpers\ApiResponse;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function access(User $user, Team $team)
    {
        if ($team->owner_id === $user->id || $team->members()->where('user_id', $user->id)->exists()) {
            return true;
        return ApiResponse::sendResponse(404, "Access denied",null);
        };
    }
}
