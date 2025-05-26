<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogLastUserActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Salva no cache a chave com o timestamp atual, expira em 5 minutos
            Cache::put('user-online-' . Auth::id(), now(), now()->addMinutes(5));
        }

        return $next($request);
    }
}
