<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrailReport extends Model
{
    protected $fillable = ['trail_id', 'user_id', 'reason','details'];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
