<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $users = User::where('id', '!=', $userId)
            ->orderBy('name')
            ->get();

        $messages = Message::with(['sender', 'receiver'])
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadMessagesCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return view('messages.index', compact('users', 'messages', 'unreadMessagesCount'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . $userId,
            'body' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt|max:5120', // max 5MB
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        Message::create([
            'sender_id' => $userId,
            'receiver_id' => $request->receiver_id,
            'body' => $request->body ?? '',
            'attachment' => $attachmentPath,
            'is_read' => false,
        ]);

        return redirect()->route('messages.show', $request->receiver_id)
            ->with('success', 'Mensagem enviada com sucesso!');
    }

    public function show($userId)
    {
        $currentUser = Auth::user();

        if ($userId == $currentUser->id) {
            return redirect()->route('messages.index');
        }

        $selectedUser = User::findOrFail($userId);

        // Marcar mensagens como lidas
        Message::where('sender_id', $selectedUser->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Buscar mensagens da conversa
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($currentUser, $selectedUser) {
                $q->where('sender_id', $currentUser->id)
                ->where('receiver_id', $selectedUser->id);
            })
            ->orWhere(function ($q) use ($currentUser, $selectedUser) {
                $q->where('sender_id', $selectedUser->id)
                ->where('receiver_id', $currentUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $users = User::where('id', '!=', $currentUser->id)->orderBy('name')->get();

        $unreadMessagesCount = Message::where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->count();

        return view('messages.index', compact('users', 'currentUser', 'selectedUser', 'messages', 'unreadMessagesCount'));
    }

    public function destroy($id)
    {
        $userId = Auth::id();

        $message = Message::findOrFail($id);

        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            abort(403, 'Acesso negado');
        }

        // Apaga arquivo se existir
        if ($message->attachment && Storage::disk('public')->exists($message->attachment)) {
            Storage::disk('public')->delete($message->attachment);
        }

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Mensagem deletada com sucesso!');
    }
}
