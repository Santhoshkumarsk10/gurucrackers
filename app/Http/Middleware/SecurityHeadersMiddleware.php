<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach essential security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Clickjacking protection: only allow same origin to frame this site
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer policy to prevent data leakage in referrer headers
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Hardware feature policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Modern OWASP guidance: disable buggy legacy XSS auditor in favor of CSP
        $response->headers->set('X-XSS-Protection', '0');

        // Content Security Policy (CSP)
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
             . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
             . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; "
             . "img-src 'self' data: blob: https:; "
             . "connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
             . "frame-ancestors 'self'; "
             . "base-uri 'self'; "
             . "form-action 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        // HTTP Strict Transport Security (HSTS) when on HTTPS
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
