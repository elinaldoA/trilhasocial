<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\User;
use App\Models\Story;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // IDs de quem o usuário segue + ele mesmo
        $idsSeguindoOuEu = $user->following->pluck('id')->push($user->id);

        // Query para trilhas (mantida igual)
        $trails = Trail::with(['user', 'likes', 'comments.user', 'images'])
            ->where(function ($query) use ($idsSeguindoOuEu) {
                $query->whereIn('user_id', $idsSeguindoOuEu)
                    ->orWhereHas('user', function ($q) {
                        $q->where('is_private', false);
                    });
            })
            ->latest()
            ->paginate(6);

        // Sugestões de usuários (mantida igual)
        $sugestoes = User::where('id', '!=', $user->id)
            ->whereNotIn('id', $user->following->pluck('id'))
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Obter usuários com stories ativos (novo)
        $storiesUsers = User::whereHas('stories', function($query) {
                $query->where('expires_at', '>', now());
            })
            ->whereIn('id', $idsSeguindoOuEu) // Apenas quem o usuário segue
            ->with(['stories' => function($query) {
                $query->where('expires_at', '>', now())
                    ->orderBy('created_at', 'desc');
            }])
            ->get()
            ->sortByDesc(function($user) {
                return $user->stories->first()->created_at;
            });

        return view('feed', compact('trails', 'sugestoes', 'storiesUsers'));
    }

    public function buscar(Request $request)
    {
        $query = $request->input('q');
        $userId = auth()->id();

        $users = User::where('id', '!=', $userId)
            ->where('name', 'like', "%{$query}%")
            ->paginate(6, ['*'], 'users_page');

        $trails = Trail::where('description', 'like', "%{$query}%")
            ->with('user')
            ->paginate(6, ['*'], 'trails_page');

        return view('busca.resultados', compact('users', 'trails', 'query'));
    }
}
