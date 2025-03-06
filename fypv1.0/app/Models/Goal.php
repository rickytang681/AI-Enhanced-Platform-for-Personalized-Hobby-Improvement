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
        'hobby_id',
        'goal',
        'progress',
        'status',
        'deadline',
        'notes'
    ];

    protected $casts = [
        'deadline' => 'date',
        'progress' => 'integer',
        'status' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($goal) {
            // This will respect soft deletes
            $goal->milestones()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hobby()
    {
        return $this->belongsTo(Hobby::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
}
