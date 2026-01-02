<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware([\App\Http\Middleware\CheckRole::class . ':admin'])
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $has = $user->roles()->where('name', $role)->exists();

        if (! $has) {
            abort(403);
        }

        return $next($request);
    }
}
