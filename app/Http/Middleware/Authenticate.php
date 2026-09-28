<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            PersistentLogin::restoreFromRequest($request);
        } catch (\Throwable $e) {
            if (!PersistentLogin::isTransient($e)) {
                throw $e;
            }
        }

        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (PersistentLogin::isAjaxRequest($request)) {
            return null;
        }

        // Cookie masih ada → jangan langsung ke login; biarkan Handler/unauthenticated handle retry.
        if (PersistentLogin::hasCookie($request)) {
            return null;
        }

        return route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if (PersistentLogin::hasCookie($request) || PersistentLogin::isAjaxRequest($request)) {
            throw new \Illuminate\Auth\AuthenticationException(
                'Unauthenticated.',
                $guards,
                null
            );
        }

        parent::unauthenticated($request, $guards);
    }
}
