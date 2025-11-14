<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles (daftar role yang diizinkan)
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user tidak login atau tidak punya role yang diizinkan
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Redirect ke dashboard, atau tampilkan halaman 403
            // return redirect('dashboard')->with('error', 'Anda tidak punya akses ke halaman ini.');
            abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI OTORISASI.');
        }

        return $next($request);
    }
}
