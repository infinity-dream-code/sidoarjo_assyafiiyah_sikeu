<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionHelper;
use App\Support\PersistentLogin;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRoleOrPermission
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        if (!Auth::check()) {
            $user = PersistentLogin::userFromRequest($request);
            if ($user) {
                PersistentLogin::bind($user);
                PersistentLogin::queue($user);
            }
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return $next($request);
        }
//        if (!$user->hasAnyRole($roles)) {
//            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
//        }
//
//        return $next($request);

        foreach ($params as $param) {
            if (method_exists($user, 'hasRole') && $user->hasRole($param)) {
                return $next($request);
            }

            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($param)) {
                return $next($request);
            }
        }

        abort(404, 'Halaman Tidak Ditemukan!');
    }
}
