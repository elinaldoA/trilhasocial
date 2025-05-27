<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrailCommentController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TrailShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('feed');
});

Route::middleware(['auth', 'verified'])->get('/feed', [FeedController::class, 'index'])->name('feed');

Route::middleware('auth')->group(function () {
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('perfil.publico');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('trails', TrailController::class);
    Route::post('/trails/{trail}/like', [TrailController::class, 'like'])->name('trails.like');
    Route::post('/comments', [TrailCommentController::class, 'store'])->name('comments.store');
    Route::post('/trails/{trail}/share', [TrailController::class, 'share'])->name('trails.share');
    Route::post('/trails/{id}/toggle', [TrailController::class, 'toggle'])->name('trails.toggle');
    Route::post('/trails/{trail}/share', [TrailShareController::class, 'share'])->name('trails.share');


    Route::post('/seguir/{user}', [FollowController::class, 'toggle'])->name('follow.toggle');
    Route::get('/{user}/seguidores', [FollowController::class, 'seguidores'])->name('follow.seguidores');
    Route::get('/{user}/seguindo', [FollowController::class, 'seguindo'])->name('follow.seguindo');
    Route::get('/sugestoes', [FollowController::class, 'sugestoes'])->name('follow.sugestoes');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/buscar', [FeedController::class, 'buscar'])->name('buscar');
    Route::get('/explorar', [FeedController::class, 'explorar'])->name('explorar');
});

require __DIR__.'/auth.php';
