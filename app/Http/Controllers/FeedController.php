<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $trails = Trail::with(['user', 'likes', 'comments.user', 'images'])
            ->latest()
            ->paginate(6);

        $sugestoes = User::where('id', '!=', auth()->id())
            ->whereNotIn('id', auth()->user()->following->pluck('id'))
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('feed', compact('trails', 'sugestoes'));
    }


    public function buscar(Request $request)
    {
        $query = $request->input('q');

        $users = User::where('name', 'like', "%{$query}%")
            ->paginate(6, ['*'], 'users_page');

        $trails = Trail::where('description', 'like', "%{$query}%")
            ->with('user')
            ->paginate(6, ['*'], 'trails_page');

        return view('busca.resultados', compact('users', 'trails', 'query'));
    }
}
