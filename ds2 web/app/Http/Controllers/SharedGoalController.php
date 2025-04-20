<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SharedGoalController extends Controller
{
    public function index()
    {
        $sharedGoals = Goal::where('visibility', 'public')
            ->orWhere(function ($query) {
                $query->where('visibility', 'friends')
                    ->whereHas('user', function ($q) {
                        $q->whereHas('friends', function ($f) {
                            $f->where('id', Auth::id());
                        });
                    });
            })
            ->with(['user', 'steps'])
            ->latest()
            ->paginate(12);

        return view('goals.shared.index', compact('sharedGoals'));
    }

    public function join(Goal $goal)
    {
        // Vérifier si l'utilisateur peut voir cet objectif
        $this->authorize('view', $goal);

        // Créer une copie de l'objectif pour l'utilisateur
        $newGoal = $goal->replicate();
        $newGoal->user_id = Auth::id();
        $newGoal->parent_goal_id = $goal->id;
        $newGoal->save();

        // Copier les étapes
        foreach ($goal->steps as $step) {
            $newStep = $step->replicate();
            $newStep->goal_id = $newGoal->id;
            $newStep->save();
        }

        return redirect()->route('goals.show', $newGoal)
            ->with('success', 'Vous avez rejoint cet objectif avec succès !');
    }

    public function participants(Goal $goal)
    {
        $this->authorize('view', $goal);

        $participants = User::with(['goals' => function ($query) use ($goal) {
            $query->where('parent_goal_id', $goal->id)
                  ->orWhere('id', $goal->id);
        }])->whereHas('goals', function ($query) use ($goal) {
            $query->where('parent_goal_id', $goal->id)
                  ->orWhere('id', $goal->id);
        })->get()
        ->map(function ($user) {
            $userGoal = $user->goals->first();
            return (object)[
                'user' => $user,
                'user_id' => $user->id,
                'progress' => $userGoal->progress,
                'created_at' => $userGoal->created_at,
                'completed_at' => $userGoal->completed_at
            ];
        });

        return view('goals.shared.participants', compact('goal', 'participants'));
    }

    public function progress(Goal $goal)
    {
        $this->authorize('view', $goal);

        $participants = User::whereHas('goals', function ($query) use ($goal) {
            $query->where('parent_goal_id', $goal->id);
        })->with(['goals' => function ($query) use ($goal) {
            $query->where('parent_goal_id', $goal->id);
        }])->get();

        $progress = $participants->map(function ($participant) {
            return [
                'name' => $participant->name,
                'progress' => $participant->goals->first()->progress
            ];
        });

        return response()->json($progress);
    }
} 