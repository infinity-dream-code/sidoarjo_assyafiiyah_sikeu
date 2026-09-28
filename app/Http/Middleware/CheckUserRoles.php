<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$params)
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

        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        foreach ($params as $param) {
            if (method_exists($user, 'hasRole') && $user->hasRole($param)) {
                return $next($request);
            }
        }

        abort(404, 'Halaman Tidak Ditemukan!');
    }
}
