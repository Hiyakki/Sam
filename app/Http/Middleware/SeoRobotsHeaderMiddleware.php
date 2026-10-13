<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoRobotsHeaderMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isProduction = app()->isProduction();

        // 1. Kondisi wajib noindex, nofollow di SEMUA environment
        $isConfirmation = $request->is('janji/konfirmasi/*');
        $isAdmin = $request->is('kelola-sam*');
        $isApi = $request->is('api/*');
        $isSearch = $request->hasAny(['cari', 'q', 'search']) && filled(
            $request->query('cari') ?? $request->query('q') ?? $request->query('search')
        );
        $isError = $response->getStatusCode() >= 400;

        if ($isConfirmation || $isAdmin || $isSearch || $isError || $isApi) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        } elseif (! $isProduction) {
            // Di lingkungan non-production (local/staging/testing), blokir perayapan pada semua halaman
            $response->headers->set('X-Robots-Tag', 'noindex');
        }

        return $response;
    }
}
