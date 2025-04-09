<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Jika tidak ada role yang diberikan, tolak akses
        if (!$user || empty($roles)) {
            abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini.');
        }

        // Ambil role user (level_kode) dan periksa apakah termasuk role yang diizinkan
        if (in_array($user->getRole(), $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki role yang sesuai, tampilkan error 403
        abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini.');
    }
}
