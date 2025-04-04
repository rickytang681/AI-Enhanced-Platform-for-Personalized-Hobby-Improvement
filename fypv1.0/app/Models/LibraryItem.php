<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'content',
        'video_url',
        'category',
        'subcategory',
        'likes',
        'dislikes',
        'file_path',
        'average_rating',
        'rating_count'
    ];

    protected $casts = [
        'likes' => 'integer',
        'dislikes' => 'integer',
        'average_rating' => 'float',
        'rating_count' => 'integer',
    ];

    // Add type validation constant
    const ALLOWED_TYPES = ['video', 'text'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($item) {
            // Delete the physical file if it exists
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }

            // Delete related comments
            $item->comments()->delete();

            // Delete related reactions
            $item->reactions()->delete();

            // Delete related ratings
            $item->ratings()->delete();

            // Delete related saves
            $item->saves()->delete();
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

    public function isSavedBy(?User $user = null)
    {
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return false;
        }

        return $this->saves()->where('user_id', $user->id)->exists();
    }

    public function userRating(?User $user = null)
    {
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return null;
        }

        return $this->ratings()->where('user_id', $user->id)->first();
    }

    public function userReaction()
    {
        return $this->reactions()
            ->where('user_id', auth()->id())
            ->first();
    }

    public function currentUserReaction()
    {
        return $this->hasOne(LibraryReaction::class)
            ->where('user_id', auth()->id());
    }

    public function updateReactionCounts()
    {
        $this->likes = $this->reactions()->where('reaction_type', 'like')->count();
        $this->dislikes = $this->reactions()->where('reaction_type', 'dislike')->count();
        $this->save();
    }

    public function updateRatingStats()
    {
        $this->average_rating = round($this->ratings()->avg('rating'), 1);
        $this->rating_count = $this->ratings()->count();
        $this->save();
    }

    public function getFileUrl()
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('likes', 'desc');
    }

    public function scopeHighestRated($query)
    {
        return $query->where('rating_count', '>', 0)
                    ->orderBy('average_rating', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}







