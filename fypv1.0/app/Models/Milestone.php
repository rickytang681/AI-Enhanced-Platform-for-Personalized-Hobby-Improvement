<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'due_date', 'completed', 'goal_id'];
    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
