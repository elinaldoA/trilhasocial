<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trail extends Model
{
    protected $fillable = [
        'name','description','location','difficulty','distance','avg_time','user_id'
    ];

    public function images()
    {
        return $this->hasMany(TrailImage::class);
    }
    public function videos()
    {
        return $this->hasMany(TrailVideo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(TrailLike::class);
    }

    public function comments()
    {
        return $this->hasMany(TrailComment::class);
    }

    public function shares()
    {
        return $this->hasMany(TrailShare::class);
    }
    public function sharedWithMe()
    {
        return $this->hasMany(SharedTrail::class);
    }

    public function storyUsers()
    {
        return $this->hasMany(Story::class);
    }
}
