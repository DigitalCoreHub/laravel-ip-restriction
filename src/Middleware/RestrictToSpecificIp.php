<?php

namespace DigitalCoreHub\LaravelIpRestriction\Middleware;

use Closure;
use DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToSpecificIp
{
    protected LaravelIpRestriction $ipRestriction;

    public function __construct(LaravelIpRestriction $ipRestriction)
    {
        $this->ipRestriction = $ipRestriction;
    }

    public function handle(Request $request, Closure $next)
    {
        $clientIp = $this->getClientIpFromRequest($request);

        if (! $clientIp || ! $this->ipRestriction->isIpAllowed($clientIp)) {
            if (config('ip-restriction.log_attempts', true)) {
                $this->logAccessAttempt($request, $clientIp);
            }

            $errorMessage = config('ip-restriction.error_message', 'Access denied. Your IP address is not authorized.');
            abort(Response::HTTP_FORBIDDEN, $errorMessage);
        }

        return $next($request);
    }

    /**
     * İstekten istemci IP adresini alır.
     * Get the client's IP address from the request.
     */
    protected function getClientIpFromRequest(Request $request): ?string
    {
        // Çeşitli header'lardan IP kontrolü (proxy arkasında kullanışlı)
        // Check for IP from various headers (useful behind proxies)
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'HTTP_CLIENT_IP',            // Proxy
            'REMOTE_ADDR',               // Standart / Standard
        ];

        foreach ($headers as $header) {
            if ($request->hasHeader($header)) {
                $ips = explode(',', $request->header($header));
                $ip = trim($ips[0]);

                if ($this->isValidIp($ip)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    /**
     * IP adresini doğrular.
     * Validate IP address.
     */
    protected function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Güvenlik izleme için erişim denemesini loglar.
     * Log access attempt for security monitoring.
     */
    protected function logAccessAttempt(Request $request, ?string $ip): void
    {
        logger()->warning('Unauthorized IP access attempt', [
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
