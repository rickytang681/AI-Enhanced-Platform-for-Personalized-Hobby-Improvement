<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'due_date',
        'completed',
        'goal_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
