<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Goal;

class BadgeService
{
    public function checkAndAwardBadges(User $user)
    {
        $this->checkProgressBadges($user);
        $this->checkAchievementBadges($user);
        $this->checkLocationBadges($user);
        $this->checkSocialBadges($user);
        $this->checkTimeBadges($user);
    }

    private function checkProgressBadges(User $user)
    {
        $goalsCount = $user->goals()->count();
        
        $badges = Badge::where('category', 'progress')
            ->whereNotIn('id', $user->badges->pluck('id'))
            ->get();

        foreach ($badges as $badge) {
            if ($goalsCount >= $badge->required_points) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function checkAchievementBadges(User $user)
    {
        $completedGoalsCount = $user->goals()
            ->where('is_completed', true)
            ->count();
        
        $badges = Badge::where('category', 'achievement')
            ->whereNotIn('id', $user->badges->pluck('id'))
            ->get();

        foreach ($badges as $badge) {
            if ($completedGoalsCount >= $badge->required_points) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function checkLocationBadges(User $user)
    {
        $uniqueLocationsCount = $user->goals()
            ->whereNotNull('location_name')
            ->distinct('location_name')
            ->count();
        
        $badges = Badge::where('category', 'location')
            ->whereNotIn('id', $user->badges->pluck('id'))
            ->get();

        foreach ($badges as $badge) {
            if ($uniqueLocationsCount >= $badge->required_points) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function checkSocialBadges(User $user)
    {
        $publicGoalsCount = $user->goals()
            ->where('visibility', 'public')
            ->count();
        
        $badges = Badge::where('category', 'social')
            ->whereNotIn('id', $user->badges->pluck('id'))
            ->get();

        foreach ($badges as $badge) {
            if ($publicGoalsCount >= $badge->required_points) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function checkTimeBadges(User $user)
    {
        $onTimeCompletedCount = $user->goals()
            ->where('is_completed', true)
            ->whereNotNull('deadline')
            ->whereRaw('updated_at <= deadline')
            ->count();
        
        $badges = Badge::where('category', 'time')
            ->whereNotIn('id', $user->badges->pluck('id'))
            ->get();

        foreach ($badges as $badge) {
            if ($onTimeCompletedCount >= $badge->required_points) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function awardBadge(User $user, Badge $badge)
    {
        if (!$user->badges->contains($badge->id)) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now()
            ]);

            // Déclencher un événement pour la notification
            event(new \App\Events\BadgeEarned($user, $badge));
        }
    }
} 