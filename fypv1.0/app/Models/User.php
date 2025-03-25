<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hobbies()
    {
        return $this->hasMany(Hobby::class);
    }

    public function libraryComments()
    {
        return $this->hasMany(LibraryComment::class);
    }

    public function libraryReactions()
    {
        return $this->hasMany(LibraryReaction::class);
    }

    public function libraryRatings()
    {
        return $this->hasMany(LibraryRating::class);
    }

    public function librarySaves()
    {
        return $this->hasMany(LibrarySave::class);
    }

    public function communityPosts()
    {
        return $this->hasMany(Community::class);
    }

    public function communityComments()
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function communityReactions()
    {
        return $this->hasMany(CommunityReaction::class);
    }

    public function communitySavedPosts()
    {
        return $this->belongsToMany(Community::class, 'community_saved_posts');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}
