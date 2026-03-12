<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cache static assets for 30 days
        if ($request->is('build/*') || $request->is('css/*') || $request->is('js/*') || $request->is('icons/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000, immutable');
        }
        // Cache API responses for 3 minutes if not logged in
        elseif ($request->is('api/*')) {
            if (!auth()->check()) {
                $response->headers->set('Cache-Control', 'public, max-age=180');
            } else {
                $response->headers->set('Cache-Control', 'private, max-age=60');
            }
        }
        // Cache HTML pages for 1 minute
        else {
            $response->headers->set('Cache-Control', 'public, max-age=60, must-revalidate');
        }

        return $response;
    }
}
