<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming API request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Ekstrak token dari Authorization header, X-API-KEY, atau query param
        $token = $request->bearerToken();

        if (empty($token)) {
            $token = $request->header('X-API-KEY');
        }

        if (empty($token)) {
            $token = $request->query('api_token');
        }

        if (empty($token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token otentikasi tidak ditemukan. Harap sertakan Bearer token atau header X-API-KEY.',
            ], 401);
        }

        // 2. Cari pengguna berdasarkan api_token dengan parameter binding
        $user = User::with('role')->where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token otentikasi tidak valid atau telah kadaluarsa.',
            ], 401);
        }

        // 3. Cek Role Privilege jika endpoint mewajibkan role tertentu
        $userRole = $user->role ? $user->role->kode_role : null;

        // Admin selalu memiliki izin
        if (!empty($roles) && $userRole !== 'admin' && !in_array($userRole, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => "Akses ditolak: Peran '{$user->role->nama_role}' tidak memiliki hak akses untuk endpoint ini.",
            ], 403);
        }

        // Simpan user ke request
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
