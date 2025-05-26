<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserFollowedNotification;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Você não pode seguir a si mesmo.');
        }

        if ($authUser->isFollowing($user)) {
            $authUser->following()->detach($user->id);
        } else {
            $authUser->following()->attach($user->id);

            // Envia notificação ao usuário seguido
            $user->notify(new UserFollowedNotification($authUser->name, $authUser->id));
        }

        return back()->with('success', 'Ação de seguir/seguir desfeita com sucesso.');
    }

    public function sugestoes()
    {
        $user = auth()->user();

        // IDs de quem o usuário atual já está seguindo
        $idsSeguindo = $user->following->pluck('id');

        // Sugestões com base em seguidores em comum
        $sugestoesRelacionadas = User::whereHas('followers', function ($query) use ($idsSeguindo) {
                $query->whereIn('follower_id', $idsSeguindo);
            })
            ->whereNotIn('id', $idsSeguindo)
            ->where('id', '!=', $user->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Novos usuários (recentes, não seguidos ainda)
        $novosUsuarios = User::whereNotIn('id', $idsSeguindo)
            ->where('id', '!=', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Mescla as sugestões
        $sugestoes = $sugestoesRelacionadas->merge($novosUsuarios)->unique('id')->take(6);

        return view('follow.sugestoes', compact('sugestoes'));
    }


    public function seguidores(User $user)
    {
        return view('follow.seguidores', ['user' => $user, 'seguidores' => $user->followers]);
    }

    public function seguindo(User $user)
    {
        return view('follow.seguindo', ['user' => $user, 'seguindo' => $user->following]);
    }
}
