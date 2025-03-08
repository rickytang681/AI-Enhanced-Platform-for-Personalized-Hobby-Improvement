<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryItem extends Model
{
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
        'average_rating',
        'rating_count'
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
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User'
        ]);
    }

    public function reactions()
    {
        return $this->hasMany(LibraryReaction::class);
    }

    public function comments()
    {
        return $this->hasMany(LibraryComment::class);
    }

    public function ratings()
    {
        return $this->hasMany(LibraryRating::class);
    }

    public function saves()
    {
        return $this->hasMany(LibrarySave::class);
    }

    public function isSavedBy(User $user)
    {
        return $this->saves()->where('user_id', $user->id)->exists();
    }

    public function userRating(User $user)
    {
        return $this->ratings()->where('user_id', $user->id)->first();
    }

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }
}
