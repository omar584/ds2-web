<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'progress',
        'deadline',
        'latitude',
        'longitude',
        'location_name',
        'visibility',
        'is_completed'
    ];

    protected $casts = [
        'deadline' => 'date',
        'progress' => 'float',
        'is_completed' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(GoalStep::class)->orderBy('order');
    }

    public function updateProgress()
    {
        $totalSteps = $this->steps()->count();
        if ($totalSteps > 0) {
            $completedSteps = $this->steps()->where('is_completed', true)->count();
            $this->progress = ($completedSteps / $totalSteps) * 100;
            $this->save();
        }
    }
} 