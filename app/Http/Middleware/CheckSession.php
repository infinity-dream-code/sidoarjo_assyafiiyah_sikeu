<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            PersistentLogin::restoreFromRequest($request);
        } catch (\Throwable $e) {
            if (!PersistentLogin::isTransient($e)) {
                throw $e;
            }
        }

        if (!Auth::check()) {
            return PersistentLogin::unauthenticatedResponse($request);
        }

        return $next($request);
    }
}
