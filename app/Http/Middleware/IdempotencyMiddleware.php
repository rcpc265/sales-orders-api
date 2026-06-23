<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if (!$idempotencyKey) {
            return $next($request);
        }

        $cacheKey = 'idempotency_' . $idempotencyKey;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $next($request);

        // Cache the response for 24 hours (86400 seconds)
        Cache::put($cacheKey, $response, 86400);

        return $response;
    }
}
