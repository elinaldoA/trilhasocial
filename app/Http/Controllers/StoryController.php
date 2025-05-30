<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    public function show(User $user)
    {
        $stories = $user->stories()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'asc')
            ->with('user')
            ->get();

        if ($stories->isEmpty()) {
            return redirect()->back()->with('error', 'Nenhum story disponível');
        }

        return view('stories.show', [
            'stories' => $stories,
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:50000', // Adicionei 'mov'
        ]);

        try {
            $file = $request->file('media');
            $type = $file->getMimeType();
            $mediaType = strpos($type, 'video') !== false ? 'video' : 'image';

            Log::info('Tentando armazenar arquivo', [
                'original_name' => $file->getClientOriginalName(),
                'type' => $type,
                'size' => $file->getSize()
            ]);

            $path = $file->store('stories', 'public');

            Log::info('Arquivo armazenado com sucesso', ['path' => $path]);

            $story = auth()->user()->stories()->create([
                'media_path' => $path,
                'media_type' => $mediaType,
                'expires_at' => now()->addHours(24),
            ]);

            return response()->json([
                'success' => true,
                'story' => $story,
                'url' => Storage::url($path) // Retorna a URL pública do arquivo
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao armazenar story', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view(Story $story)
    {
        if (!$story->viewers()->where('user_id', auth()->id())->exists()) {
            $story->viewers()->attach(auth()->id());
        }

        return response()->json([
            'viewers' => $story->viewers,
            'viewers_count' => $story->viewers()->count()
        ]);
    }
}
