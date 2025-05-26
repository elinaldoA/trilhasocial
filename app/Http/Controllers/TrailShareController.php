<?php

namespace App\Http\Controllers;

use App\Models\Trail;

class TrailShareController extends Controller
{
    public function store(Trail $trail)
    {
        $trail->shares()->create([
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Trilha compartilhada!');
    }
}
