<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailVideo extends Model
{
    use HasFactory;

    protected $fillable = ['trail_id', 'path'];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }
}
