<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrailImage extends Model
{
    protected $fillable = ['trail_id', 'path'];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }
}
