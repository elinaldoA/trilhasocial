<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'bio',
        'location',
        'website',
        'profile_photo_path',
        'cover_photo_path',
        'last_activity',
        'is_private',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'password' => 'hashed',
    ];
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')
            ->withPivot('status')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }
    public function followRequests()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withPivot('status')
            ->wherePivot('status', 'pending')
            ->withTimestamps();
    }
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }
    public function hasPendingRequestTo(User $user): bool
    {
        return DB::table('followers')
            ->where('follower_id', $this->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }
    public function hasPendingRequestFrom(User $user): bool
    {
        return DB::table('followers')
            ->where('follower_id', $user->id)
            ->where('user_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }
    public function notifications()
    {
        return $this->morphMany(\App\Models\Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }
    public function isOnline(): bool
    {
        return $this->last_activity && $this->last_activity->gt(now()->subMinutes(5));
    }
    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
    public function coverPhotoUrl(): string
    {
        if ($this->cover_photo_path) {
            return asset('storage/' . $this->cover_photo_path);
        }

        return asset('images/default-cover.jpg');
    }
}
