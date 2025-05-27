<?php

namespace App\Http\Controllers;

use App\Models\SharedTrail;
use App\Models\Trail;
use App\Models\Message; // Importa a model de mensagens
use Illuminate\Http\Request;

class TrailShareController extends Controller
{
    public function store(Trail $trail)
    {
        $trail->shares()->create([
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Trilha compartilhada!');
    }

    public function share(Request $request, Trail $trail)
    {
        $request->validate([
            'followers' => 'required|array',
            'followers.*' => 'exists:users,id',
        ]);

        foreach ($request->followers as $followerId) {
            SharedTrail::create([
                'trail_id' => $trail->id,
                'shared_by' => auth()->id(),
                'shared_to' => $followerId,
            ]);

            $trailUrl = route('trails.show', $trail);

            $messageBody = "Compartilhei a trilha: \"<a href='{$trailUrl}' target='_blank'>{$trail->description}</a>\" com você.";

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $followerId,
                'body' => $messageBody,
                'is_read' => false,
                'trail_id' => $trail->id,
            ]);

        }

        return back()->with('success', 'Trilha compartilhada com sucesso e mensagem enviada no chat!');
    }
}
