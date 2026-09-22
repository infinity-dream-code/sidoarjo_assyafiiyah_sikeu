<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class RetryTransientGet
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            if (!$this->shouldRetry($request, $e)) {
                throw $e;
            }

            $request->attributes->set('_transient_retried', true);
            usleep(150000);

            return $next($request);
        }
    }

    private function shouldRetry(Request $request, Throwable $e): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->attributes->get('_transient_retried')) {
            return false;
        }

        if ($e instanceof HttpExceptionInterface
            || $e instanceof TokenMismatchException
            || $e instanceof AuthenticationException
            || $e instanceof ValidationException) {
            return false;
        }

        return PersistentLogin::isTransient($e);
    }
}
