<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserFollowedNotification;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Você não pode seguir a si mesmo.');
        }

        $relation = $authUser->following()->where('user_id', $user->id)->first();

        if ($relation) {
            $authUser->following()->detach($user->id);
            return back()->with('success', 'Você deixou de seguir ' . $user->name . '.');
        }

        $status = $user->is_private ? 'pending' : 'accepted';
        $authUser->following()->attach($user->id, ['status' => $status]);

        if ($status === 'accepted') {
            $user->notify(new UserFollowedNotification($authUser->name, $authUser->id));
        }

        return back()->with('success', $status === 'pending'
            ? 'Solicitação enviada para ' . $user->name . '. Aguarde aprovação.'
            : 'Você começou a seguir ' . $user->name . '!');
    }

    public function solicitacoes()
    {
        $authUser = auth()->user();
        $solicitacoes = $authUser->followRequests()->get();

        return view('follow.solicitacoes', compact('solicitacoes'));
    }

    public function aceitarPedido(User $user)
    {
        $authUser = auth()->user();

        $exists = DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Nenhuma solicitação pendente desse usuário.');
        }

        DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->update(['status' => 'accepted']);

        // $user->notify(new UserFollowedNotification($authUser->name, $authUser->id));

        return back()->with('success', 'Você aceitou a solicitação de ' . $user->name . '.');
    }

    public function rejeitarPedido(User $user)
    {
        $authUser = auth()->user();

        $exists = DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Nenhuma solicitação pendente desse usuário.');
        }

        DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->delete();

        return back()->with('success', 'Você rejeitou a solicitação de ' . $user->name . '.');
    }

    public function removerSeguidor(User $user)
    {
        $authUser = auth()->user();

        $exists = DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Este usuário não é seu seguidor.');
        }

        DB::table('followers')
            ->where('user_id', $authUser->id)
            ->where('follower_id', $user->id)
            ->delete();

        return back()->with('success', 'Você removeu ' . $user->name . ' dos seus seguidores.');
    }

    public function sugestoes()
    {
        $user = auth()->user();
        $idsSeguindo = $user->following->pluck('id');

        $sugestoesRelacionadas = User::whereHas('followers', function ($query) use ($idsSeguindo) {
            $query->whereIn('follower_id', $idsSeguindo);
        })
            ->whereNotIn('id', $idsSeguindo)
            ->where('id', '!=', $user->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        $novosUsuarios = User::whereNotIn('id', $idsSeguindo)
            ->where('id', '!=', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $sugestoes = $sugestoesRelacionadas->merge($novosUsuarios)->unique('id')->take(6);

        return view('follow.sugestoes', compact('sugestoes'));
    }

    public function seguidores(User $user)
    {
        return view('follow.seguidores', [
            'user' => $user,
            'seguidores' => $user->followers()->wherePivot('status', 'accepted')->get()
        ]);
    }

    public function seguindo(User $user)
    {
        return view('follow.seguindo', [
            'user' => $user,
            'seguindo' => $user->following()->wherePivot('status', 'accepted')->get()
        ]);
    }
}
