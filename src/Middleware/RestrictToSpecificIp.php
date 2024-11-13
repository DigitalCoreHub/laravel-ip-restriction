<?php

namespace DigitalCoreHub\LaravelIpRestriction\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToSpecificIp
{
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = config('ip-restriction.allowed_ips', []);

        if (!in_array($request->ip(), $allowedIps)) {
            abort(Response::HTTP_FORBIDDEN, 'Erişim izniniz bulunmamaktadır.');
        }

        return $next($request);
    }
}