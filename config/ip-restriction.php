<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed IP Addresses
    |--------------------------------------------------------------------------
    |
    | This array contains the IP addresses that are allowed to access your
    | application when using the IP restriction middleware. You can specify
    | individual IPs or use CIDR notation for IP ranges.
    |
    | Examples:
    | '127.0.0.1'           - Single IP
    | '192.168.1.0/24'      - IP range (192.168.1.1 to 192.168.1.254)
    | '10.0.0.0/8'          - Large IP range
    | '2001:db8::/32'       - IPv6 range
    |
    */

    'allowed_ips' => [
        '127.0.0.1',        // Localhost
        '::1',              // IPv6 localhost
        // '192.168.1.0/24', // Example: Local network range
        // '10.0.0.0/8',     // Example: Private network range
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how long the allowed IPs list should be cached.
    | Set to null to disable caching.
    |
    */

    'cache_ttl' => 3600, // 1 hour in seconds

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether to log unauthorized access attempts.
    |
    */

    'log_attempts' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Error Message
    |--------------------------------------------------------------------------
    |
    | Customize the error message shown when access is denied.
    |
    */

    'error_message' => 'Access denied. Your IP address is not authorized.',
];
