<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Applies baseline security headers to every response.
     *
     * - X-Frame-Options: stops the admin panel being embedded in a
     *   hidden <iframe> on an attacker's site (clickjacking).
     * - X-Content-Type-Options: stops the browser from guessing/
     *   re-interpreting a response's content type.
     * - Referrer-Policy: strips the full URL (including any query
     *   string, e.g. the ?email= on password-reset links) from the
     *   Referer header sent to third-party resources/links.
     * - Content-Security-Policy: baseline policy — restricts script/
     *   style/img sources. 'unsafe-inline' is required for now since
     *   the app has inline <script>/<style> blocks throughout the
     *   Blade views; tightening this further is a larger follow-up
     *   task (moving inline JS/CSS into files).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' ".
                'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; '.
            "style-src 'self' 'unsafe-inline' ".
                'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com '.
                'https://code.ionicframework.com; '.
            "font-src 'self' ".
                'https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; '.
            "img-src 'self' data: https:; ".
            "frame-ancestors 'self';"
        );

        return $response;
    }
}
