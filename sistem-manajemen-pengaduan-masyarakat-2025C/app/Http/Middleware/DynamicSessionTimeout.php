<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DynamicSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $lastActivity = session('last_activity_time');
            $currentTime = now();

            // Set timeout based on role (in minutes)
            // Admin & Instansi: 12 hours (720 mins)
            // Masyarakat: 24 hours (1440 mins - matching .env default)
            $timeout = ($user->isAdmin() || $user->isInstansi()) ? 720 : 1440;

            if ($lastActivity) {
                $idleMinutes = $currentTime->diffInMinutes($lastActivity);

                if ($idleMinutes > $timeout) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('status', 'Sesi Anda telah berakhir karena tidak ada aktivitas selama ' . ($timeout / 60) . ' jam.');
                }
            }

            // Update last activity time
            session(['last_activity_time' => $currentTime]);
        }

        return $next($request);
    }
}
