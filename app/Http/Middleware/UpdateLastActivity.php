<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateLastActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Salva no cache que o usuário está online por 5 minutos
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        }

        return $next($request);
    }
}
