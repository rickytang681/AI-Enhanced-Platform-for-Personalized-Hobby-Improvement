<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hobbies',
        'goal',
        'progress',
        'status',
        'deadline',
        'notes'
    ];

    protected $casts = [
        'hobbies' => 'array',
        'deadline' => 'date'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Hobby
    public function hobby()
    {
        return $this->belongsTo(Hobby::class);
    }
}
