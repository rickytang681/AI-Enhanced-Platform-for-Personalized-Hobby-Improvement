<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'cover_image',
        'tag',
        'post_type', // 'question', 'experience', 'discussion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
