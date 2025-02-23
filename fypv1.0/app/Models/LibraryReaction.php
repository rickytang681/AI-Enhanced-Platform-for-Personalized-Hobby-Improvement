<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'library_item_id',
        'reaction_type', // 'like' or 'dislike'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libraryItem()
    {
        return $this->belongsTo(LibraryItem::class);
    }
} 