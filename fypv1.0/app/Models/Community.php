<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'post_type',
        'tag',
        'cover_image'
    ];

    protected $appends = ['likes_count', 'dislikes_count', 'user_reaction'];

    // Define table name explicitly
    protected $table = 'communities';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class)->with('user')->latest();
    }

    public function reactions()
    {
        return $this->hasMany(CommunityReaction::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'community_saved_posts');
    }

    public function saves()
    {
        return $this->hasMany(CommunitySavedPost::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('reaction_type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->reactions()->where('reaction_type', 'dislike')->count();
    }

    public function getUserReactionAttribute()
    {
        if (!auth()->check()) return null;
        return $this->reactions()->where('user_id', auth()->id())->first();
    }

    // Add this method to check if a post is saved by a user
    public function isSavedBy($user)
    {
        if (!$user) return false;
        return $this->savedByUsers()->where('user_id', $user->id)->exists();
    }
}

