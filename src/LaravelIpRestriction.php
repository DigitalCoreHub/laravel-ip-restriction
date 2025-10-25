<?php

namespace DigitalCoreHub\LaravelIpRestriction;

use Illuminate\Support\Facades\Cache;

class LaravelIpRestriction
{
    /**
     * Verilen IP adresinin izin verilen listede olup olmadığını kontrol eder.
     * Check if the given IP address is allowed.
     */
    public function isIpAllowed(string $ip): bool
    {
        $allowedIps = $this->getAllowedIps();

        foreach ($allowedIps as $allowedIp) {
            if ($this->matchesIp($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * İstekten istemci IP adresini alır.
     * Get the client's IP address from the request.
     */
    public function getClientIp(): ?string
    {
        $request = request();

        if (! $request) {
            return null;
        }

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
     * IP adresinin bir desenle eşleşip eşleşmediğini kontrol eder (CIDR notasyonu destekler).
     * Check if an IP matches a pattern (supports CIDR notation).
     */
    protected function matchesIp(string $ip, string $pattern): bool
    {
        if (! $this->isValidIp($ip)) {
            return false;
        }

        // Doğrudan IP eşleşmesi / Direct IP match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notasyonu desteği / CIDR notation support
        if (str_contains($pattern, '/')) {
            return $this->ipInRange($ip, $pattern);
        }

        return false;
    }

    /**
     * IP adresinin CIDR aralığında olup olmadığını kontrol eder.
     * Check if IP is in CIDR range.
     */
    protected function ipInRange(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);

        if (! $this->isValidIp($subnet)) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int) $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
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
     * Konfigürasyondan izin verilen IP'leri cache ile birlikte alır.
     * Get allowed IPs from config with caching.
     */
    protected function getAllowedIps(): array
    {
        $cacheTtl = config('ip-restriction.cache_ttl', 3600);

        if ($cacheTtl === null) {
            return config('ip-restriction.allowed_ips', []);
        }

        return Cache::remember('ip-restriction.allowed_ips', $cacheTtl, function () {
            return config('ip-restriction.allowed_ips', []);
        });
    }

    /**
     * IP kısıtlama cache'ini temizler.
     * Clear the IP restriction cache.
     */
    public function clearCache(): bool
    {
        return Cache::forget('ip-restriction.allowed_ips');
    }

    /**
     * İzin verilen listeye IP ekler (sadece çalışma zamanında).
     * Add an IP to the allowed list (runtime only).
     */
    public function addAllowedIp(string $ip): bool
    {
        if (! $this->isValidIp($ip)) {
            return false;
        }

        $allowedIps = $this->getAllowedIps();

        if (! in_array($ip, $allowedIps)) {
            $allowedIps[] = $ip;
            Cache::put('ip-restriction.allowed_ips', $allowedIps, 3600);
        }

        return true;
    }

    /**
     * İzin verilen listeden IP çıkarır (sadece çalışma zamanında).
     * Remove an IP from the allowed list (runtime only).
     */
    public function removeAllowedIp(string $ip): bool
    {
        $allowedIps = $this->getAllowedIps();
        $key = array_search($ip, $allowedIps);

        if ($key !== false) {
            unset($allowedIps[$key]);
            Cache::put('ip-restriction.allowed_ips', array_values($allowedIps), 3600);

            return true;
        }

        return false;
    }
}
