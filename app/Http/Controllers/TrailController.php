<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\TrailImage;
use App\Models\TrailReport;
use App\Models\TrailVideo;
use App\Notifications\TrailCommentNotification;
use App\Notifications\TrailInteractionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrailController extends Controller
{
    public function index(Request $request)
    {
        $query = Trail::with(['user', 'likes', 'comments.user', 'images', 'videos']);

        $trails = $query->latest()->paginate(6);

        return view('trails.index', compact('trails'));
    }

    public function create()
    {
        return view('trails.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'difficulty' => 'required|in:Fácil,Médio,Difícil',
            'distance' => 'required|numeric|min:0',
            'avg_time' => 'required|integer|min:1',
            'images.*' => 'nullable|image|max:2048',
            'videos.*' => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:10240',
        ]);

        $data['user_id'] = auth()->id();

        $trail = Trail::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('trails', 'public');
                TrailImage::create([
                    'trail_id' => $trail->id,
                    'path' => $path,
                ]);
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $path = $video->store('trail_videos', 'public');
                TrailVideo::create([
                    'trail_id' => $trail->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('feed')->with('success', 'Trilha criada com sucesso!');
    }

    public function show(Trail $trail)
    {
        $trail->load(['images', 'videos']);
        $trail->loadCount(['likes', 'comments', 'shares']);

        return view('trails.show', compact('trail'));
    }

    public function edit(Trail $trail)
    {
        if ($trail->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('trails.edit', compact('trail'));
    }

    public function update(Request $request, Trail $trail)
    {
        if ($trail->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'difficulty' => 'required|in:Fácil,Médio,Difícil',
            'distance' => 'required|numeric|min:0',
            'avg_time' => 'required|integer|min:1',
            'description' => 'required|string',
            'images.*' => 'nullable|image|max:2048',
            'videos.*' => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:10240',
        ]);

        $trail->update($data);

        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageId) {
                $image = $trail->images()->where('id', $imageId)->first();
                if ($image) {
                    Storage::delete('public/' . $image->path);
                    $image->delete();
                }
            }
        }

        if ($request->has('remove_videos')) {
            foreach ($request->remove_videos as $videoId) {
                $video = $trail->videos()->where('id', $videoId)->first();
                if ($video) {
                    Storage::delete('public/' . $video->path);
                    $video->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedImage) {
                $path = $uploadedImage->store('trails', 'public');
                $trail->images()->create(['path' => $path]);
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $uploadedVideo) {
                $path = $uploadedVideo->store('trail_videos', 'public');
                $trail->videos()->create(['path' => $path]);
            }
        }

        return redirect()->route('feed', $trail)->with('success', 'Trilha atualizada com sucesso!');
    }

    public function destroy(Trail $trail)
    {
        if ($trail->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        foreach ($trail->images as $image) {
            Storage::delete('public/' . $image->path);
            $image->delete();
        }

        foreach ($trail->videos as $video) {
            Storage::delete('public/' . $video->path);
            $video->delete();
        }

        $trail->delete();
        return redirect()->route('feed')->with('success', 'Trilha removida!');
    }

    public function like(Trail $trail)
    {
        $userId = auth()->id();
        $user = auth()->user();

        $like = $trail->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
        } else {
            $trail->likes()->create(['user_id' => $userId]);

            if ($trail->user_id !== $userId) {
                $trail->user->notify(new TrailInteractionNotification('like', [
                    'user_name' => $user->name,
                    'trail_id' => $trail->id,
                ]));
            }
        }

        return back()->with('success', 'Interação registrada!');
    }

    public function toggle($id)
    {
        $user = auth()->user();
        $trail = Trail::findOrFail($id);

        $like = $trail->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $mensagem = 'Você descurtiu a trilha.';
        } else {
            $trail->likes()->create(['user_id' => $user->id]);
            $mensagem = 'Você curtiu a trilha!';

            if ($trail->user_id !== $user->id) {
                $trail->user->notify(new TrailInteractionNotification('like', [
                    'user_name' => $user->name,
                    'trail_id' => $trail->id,
                ]));
            }
        }

        return back()->with('success', $mensagem);
    }


    public function comment(Request $request, Trail $trail)
    {
        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $trail->comments()->create([
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        if ($trail->user_id !== auth()->id()) {
            $trail->user->notify(new TrailCommentNotification(
                auth()->user()->name,
                $trail->id,
                $comment->body
            ));
        }

        return back()->with('success', 'Comentário enviado!');
    }

    public function share(Trail $trail)
    {
        $trail->shares()->create([
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Trilha compartilhada!');
    }

    public function report(Request $request, Trail $trail)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
            'details' => 'nullable|string|max:1000',
        ]);

        $alreadyReported = TrailReport::where('trail_id', $trail->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyReported) {
            return back()->withErrors('Você já denunciou esta trilha anteriormente.');
        }

        TrailReport::create([
            'trail_id' => $trail->id,
            'user_id' => auth()->id(),
            'reason' => $data['reason'],
            'details' => $data['details'],
        ]);

        return back()->with('success', 'Trilha reportada com sucesso. Obrigado por nos avisar!');
    }
}
