<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $viewer = auth()->user();

        $isOwner = $viewer && $viewer->id === $user->id;
        $isFollower = $viewer && $user->followers()
            ->where('follower_id', $viewer->id)
            ->wherePivot('status', 'accepted')
            ->exists();

        $hasPendingRequest = $viewer && $user->followers()
            ->where('follower_id', $viewer->id)
            ->wherePivot('status', 'pending')
            ->exists();

        // Se o perfil é privado e o viewer não é o dono nem um seguidor aceito
        if ($user->is_private && !$isOwner && !$isFollower) {
            return view('profile.privado', [
                'user' => $user,
                'hasPendingRequest' => $hasPendingRequest
            ]);
        }

        // Caso seja público ou o usuário tenha acesso
        $trails = Trail::where('user_id', $user->id)
            ->latest()
            ->with('images', 'likes', 'comments')
            ->get();

        return view('profile.publico', compact('user', 'trails'));
    }


    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo_path')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        if ($request->hasFile('cover_photo_path')) {
            if ($user->cover_photo_path) {
                Storage::disk('public')->delete($user->cover_photo_path);
            }

            $user->cover_photo_path = $request->file('cover_photo_path')->store('cover-photos', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
