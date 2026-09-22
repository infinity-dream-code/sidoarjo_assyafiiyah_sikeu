<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Lepas file-lock session lebih awal agar request paralel
 * (DataTables, filter, navigasi menu) tidak saling tabrak.
 */
class ReleaseSessionLock
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRelease($request)) {
            try {
                if ($request->hasSession()) {
                    $request->session()->save();
                }
            } catch (Throwable) {
            }
        }

        return $next($request);
    }

    private function shouldRelease(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false;
        }

        if ($request->isMethod('GET')) {
            return true;
        }

        if ($request->ajax()
            || $request->expectsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $path = $request->path();
            foreach (['get-data', 'get-column', 'get-siswa', 'get-tagihan', 'get-saldo'] as $needle) {
                if (str_contains($path, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
