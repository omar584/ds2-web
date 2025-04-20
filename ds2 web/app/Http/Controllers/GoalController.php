<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $goals = Auth::user()->goals()
            ->with('steps')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'deadline' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'visibility' => 'required|in:private,friends,public',
        ]);

        $goal = Auth::user()->goals()->create($validated);

        if ($request->has('steps')) {
            foreach ($request->steps as $index => $step) {
                $goal->steps()->create([
                    'title' => $step['title'],
                    'description' => $step['description'] ?? null,
                    'order' => $index,
                    'due_date' => $step['due_date'] ?? null,
                ]);
            }
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif créé avec succès !');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        $goal->load('steps');
        
        return view('goals.show', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'deadline' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'visibility' => 'required|in:private,friends,public',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif mis à jour avec succès !');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé avec succès !');
    }

    public function toggleStep(Goal $goal, GoalStep $step)
    {
        $this->authorize('update', $goal);

        $step->update([
            'is_completed' => !$step->is_completed
        ]);

        return response()->json([
            'success' => true,
            'progress' => $goal->progress
        ]);
    }

    public function map()
    {
        $goals = Auth::user()->goals()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('goals.map', compact('goals'));
    }
} 