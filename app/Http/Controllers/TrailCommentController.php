<?php

namespace App\Http\Controllers;
use App\Models\TrailComment;
use Illuminate\Http\Request;

class TrailCommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:trail_comments,id'
        ]);

        TrailComment::create([
            'trail_id' => $request->trail_id,
            'user_id' => auth()->id(),
            'body' => $request->body,
            'parent_id' => $request->parent_id
        ]);

        return back();
    }

}
