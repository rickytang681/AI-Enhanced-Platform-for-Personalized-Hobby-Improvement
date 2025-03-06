<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Hobby extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'experience_level',
        'user_id'
    ];

    /**
     * Get the user that owns the hobby.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the goals associated with the hobby.
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($hobby) {
            // Begin a database transaction
            DB::beginTransaction();
            try {
                // Get all goals associated with this hobby
                $goals = $hobby->goals()->get();
                
                foreach($goals as $goal) {
                    // Delete all milestones for each goal
                    $goal->milestones()->delete();
                    // Delete the goal
                    $goal->delete();
                }
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }
}
