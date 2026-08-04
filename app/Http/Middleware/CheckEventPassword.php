<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEventPassword
{
    /**
     * Handle an incoming request.
     * Pastikan peserta sudah memverifikasi password event (jika ada) sebelum bisa mengakses halaman ujian.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        if (!$event) {
            return $next($request);
        }

        // Jika event tidak punya password, lanjutkan
        if (blank($event->password_event)) {
            return $next($request);
        }

        // Cek session apakah password sudah diverifikasi
        $verified = session('event_password_verified_' . $event->id, false);

        if (!$verified) {
            return redirect()->route('ujian.index')
                ->with('error', 'Silakan masukkan password event terlebih dahulu.');
        }

        return $next($request);
    }
}

