<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // 'video' or 'text'
        'content', // For text content
        'video_url', // For video content
        'category',
        'subcategory',
        'likes',
        'dislikes',
        'file_path', // For uploaded files
    ];

    // Add this to handle JSON serialization of null values
    protected $casts = [
        'likes' => 'integer',
        'dislikes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Delete associated file when the library item is deleted
        static::deleting(function($item) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(LibraryReaction::class);
    }
} 