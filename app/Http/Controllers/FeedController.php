<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // IDs dos usuários que ele segue + ele mesmo
    $idsSeguindoOuEu = $user->following->pluck('id')->push($user->id);

    // Busca trilhas de:
    // - Usuários que ele segue ou ele mesmo
    // - Ou de usuários públicos
    $trails = Trail::with(['user', 'likes', 'comments.user', 'images'])
        ->where(function ($query) use ($idsSeguindoOuEu) {
            $query->whereIn('user_id', $idsSeguindoOuEu)
                  ->orWhereHas('user', function ($q) {
                      $q->where('is_private', false);
                  });
        })
        ->latest()
        ->paginate(6);

    // Sugestões de usuários que ele ainda não segue
    $sugestoes = User::where('id', '!=', $user->id)
        ->whereNotIn('id', $user->following->pluck('id'))
        ->inRandomOrder()
        ->take(5)
        ->get();

    return view('feed', compact('trails', 'sugestoes'));
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
