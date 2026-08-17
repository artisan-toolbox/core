<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaCrossDomainVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return match (true) {
            ! $request->headers->has('X-Inertia'),
            ! $request->isMethod($request::METHOD_GET),
            $request->host() === $this->previousHost() => $next($request),
            default => Inertia::location($request->fullUrl()),
        };
    }

    protected function previousHost(): ?string
    {
        $host = parse_url(url()->previous(), PHP_URL_HOST);

        return is_string($host) ? $host : null;
    }
}
