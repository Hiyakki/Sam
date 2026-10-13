<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SeoRedirectMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya proses request GET dan HEAD untuk redirect SEO
        if (! $request->isMethodSafe()) {
            return $next($request);
        }

        // Abaikan route internal Livewire, asset build, storage privat, dan alur janji temu
        if ($request->is('livewire/*', '_debugbar/*', 'build/*', 'storage/*', 'janji/*')) {
            return $next($request);
        }

        $path = $request->getPathInfo();
        $uri = $request->getRequestUri();

        // 1. Cek tabel database redirects untuk URL saat ini (path atau full uri)
        $customRedirect = $this->findRedirect($path, $uri);
        if ($customRedirect) {
            $target = $customRedirect->target_path;
            $statusCode = $customRedirect->status_code ?? 301;

            return redirect()->to(url($target), $statusCode);
        }

        // 2. Normalisasi trailing slash dan huruf kapital dalam satu lompatan
        $hasTrailingSlash = $path !== '/' && str_ends_with($path, '/');
        $trimmedPath = $hasTrailingSlash ? rtrim($path, '/') : $path;

        // Cek apakah ada huruf kapital pada path
        $hasUppercase = (bool) preg_match('/[A-Z]/', $trimmedPath);
        $normalizedPath = $hasUppercase ? strtolower($trimmedPath) : $trimmedPath;

        // Cek kembali tabel redirects untuk normalizedPath jika berbeda
        if ($normalizedPath !== $path) {
            $normalizedRedirect = $this->findRedirect($normalizedPath, $normalizedPath);
            if ($normalizedRedirect) {
                return redirect()->to(url($normalizedRedirect->target_path), $normalizedRedirect->status_code ?? 301);
            }

            // Bentuk URL bersih
            $queryString = $request->getQueryString();
            $targetUrl = url($normalizedPath).($queryString ? '?'.$queryString : '');

            return redirect()->to($targetUrl, 301);
        }

        // 3. Normalisasi Host & Skema sesuai config('app.url') (hanya di production)
        if (app()->isProduction()) {
            $appUrl = config('app.url');
            $parsedAppUrl = parse_url($appUrl);
            $canonicalHost = $parsedAppUrl['host'] ?? null;
            $canonicalScheme = $parsedAppUrl['scheme'] ?? 'https';

            $currentHost = $request->getHost();
            $currentScheme = $request->getScheme();

            if ($canonicalHost && ($currentHost !== $canonicalHost || $currentScheme !== $canonicalScheme)) {
                $targetUrl = $canonicalScheme.'://'.$canonicalHost.$request->getRequestUri();

                return redirect()->to($targetUrl, 301);
            }
        }

        return $next($request);
    }

    /**
     * Cari redirect aktif di tabel database dengan aman.
     */
    protected function findRedirect(string $path, string $uri): ?Redirect
    {
        try {
            if (! Schema::hasTable('redirects')) {
                return null;
            }

            return Redirect::where('source_path', $uri)
                ->orWhere('source_path', $path)
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
