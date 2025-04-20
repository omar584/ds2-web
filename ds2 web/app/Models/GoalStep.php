<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'order',
        'is_completed',
        'due_date'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date'
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($step) {
            $step->goal->updateProgress();
        });
    }
} 