<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarySave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'library_item_id'
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