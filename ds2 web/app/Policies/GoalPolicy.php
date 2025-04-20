<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoalPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Goal $goal)
    {
        if ($user->id === $goal->user_id) {
            return true;
        }

        return match ($goal->visibility) {
            'public' => true,
            'friends' => $user->isFriendWith($goal->user),
            'private' => false,
            default => false,
        };
    }

    public function update(User $user, Goal $goal)
    {
        return $user->id === $goal->user_id;
    }

    public function delete(User $user, Goal $goal)
    {
        return $user->id === $goal->user_id;
    }
} 