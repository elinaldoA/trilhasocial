<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    protected $fillable = ['user_id', 'media_path', 'media_type', 'expires_at'];

    protected $dates = ['expires_at'];

    protected $appends = ['viewers_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'story_views')
            ->withTimestamps();
    }

    public function getViewersCountAttribute()
    {
        return $this->viewers()->count();
    }

    public function getMediaUrlAttribute()
    {
        return Storage::url($this->media_path);
    }
}
