<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrailCommentController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoryController;
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
    Route::post('/trails/{trail}/report', [TrailController::class, 'report'])->name('trails.report');

    Route::post('/seguir/{user}', [FollowController::class, 'toggle'])->name('follow.toggle');
    Route::get('/{user}/seguidores', [FollowController::class, 'seguidores'])->name('follow.seguidores');
    Route::get('/{user}/seguindo', [FollowController::class, 'seguindo'])->name('follow.seguindo');
    Route::get('/sugestoes', [FollowController::class, 'sugestoes'])->name('follow.sugestoes');
    Route::get('/solicitacoes', [FollowController::class, 'solicitacoes'])->name('follow.solicitacoes');
    Route::post('/solicitacoes/{user}/aceitar', [FollowController::class, 'aceitarPedido'])->name('follow.aceitar');
    Route::post('/solicitacoes/{user}/rejeitar', [FollowController::class, 'rejeitarPedido'])->name('follow.rejeitar');
    // Remover um seguidor que te segue
    Route::post('/follow/remover-seguidor/{user}', [FollowController::class, 'removerSeguidor'])->name('follow.remover_seguidor');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/buscar', [FeedController::class, 'buscar'])->name('buscar');
    Route::get('/explorar', [FeedController::class, 'explorar'])->name('explorar');

    Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/{user}', [StoryController::class, 'show'])->name('stories.show');
    Route::post('/stories', [StoryController::class, 'store']);
    Route::post('/stories/{story}/view', [StoryController::class, 'view']);
});

require __DIR__.'/auth.php';
