<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailComment extends Model
{
    use HasFactory;

    protected $fillable = ['trail_id', 'user_id', 'body'];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function replies()
    {
        return $this->hasMany(TrailComment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(TrailComment::class, 'parent_id');
    }

}
