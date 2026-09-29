<?php

namespace App\Http\Middleware;

use App\Models\StoreVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackStoreVisit
{
    /**
     * Handle an incoming request and track customer portal visits.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests on the customer storefront
        if ($request->isMethod('GET') && $response->getStatusCode() < 400) {
            if ($this->shouldTrack($request)) {
                StoreVisit::recordVisit($request);
            }
        }

        return $response;
    }

    /**
     * Determine if the request should be counted as a customer portal visit.
     */
    protected function shouldTrack(Request $request): bool
    {
        // Skip admin routes
        if ($request->is('admin*') || $request->routeIs('admin.*')) {
            return false;
        }

        // Skip logged-in admins to avoid inflating real customer visit metrics
        if (Auth::check()) {
            return false;
        }

        // Skip APIs, webhooks, static files, health checks
        if ($request->is('api*') || $request->is('storage*') || $request->is('up') || $request->is('sounds*')) {
            return false;
        }

        // Skip search engine crawlers and automated bots
        $userAgent = (string) $request->userAgent();
        if (empty($userAgent) || preg_match('/(bot|crawl|spider|slurp|curl|wget|facebookexternalhit|whatsapp)/i', $userAgent)) {
            return false;
        }

        return true;
    }
}
