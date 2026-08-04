<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIntranet
{
    /**
     * Intranet IP ranges (private/local network ranges).
     * Only requests from these IPs are allowed access.
     */
    private array $intranetRanges = [
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '127.0.0.0/8',      // localhost
        '::1',              // IPv6 localhost
    ];

    /**
     * Handle an incoming request.
     * Block access if the request is not from an intranet IP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (!$this->isIntranetIp($ip)) {
            abort(403, 'Akses hanya diperbolehkan dari jaringan internal (intranet).');
        }

        return $next($request);
    }

    /**
     * Check if the given IP belongs to an intranet range.
     */
    private function isIntranetIp(string $ip): bool
    {
        // Check IPv6 localhost
        if ($ip === '::1') {
            return true;
        }

        foreach ($this->intranetRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IPv4 address is within a CIDR range.
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (str_contains($range, '/')) {
            [$subnet, $bits] = explode('/', $range);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - (int) $bits);
            $subnet &= $mask;
            return ($ip & $mask) === $subnet;
        }

        return $ip === $range;
    }
}

