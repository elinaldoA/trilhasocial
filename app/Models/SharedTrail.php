<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedTrail extends Model
{
    protected $fillable = [
        'trail_id',
        'shared_by',
        'shared_to',
    ];
}
