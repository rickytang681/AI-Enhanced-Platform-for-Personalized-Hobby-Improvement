<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'library_item_id',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User'
        ]);
    }

    public function libraryItem()
    {
        return $this->belongsTo(LibraryItem::class);
    }
} 