<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * POST /api/login
     * Otentikasi dan penerbitan API Token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 400);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        $user = User::with('role')
            ->where('nim_nik', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kombinasi NIM/Email atau password salah.',
            ], 401);
        }

        // Generate / refresh token
        if (empty($user->api_token)) {
            $user->api_token = 'tok_' . Str::random(40);
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $user->api_token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'nim_nik' => $user->nim_nik,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'role' => $user->role ? $user->role->kode_role : null,
                'nama_role' => $user->role ? $user->role->nama_role : null,
                'foto_url' => $user->foto_url,
            ],
        ], 200);
    }

    /**
     * GET /api/user
     * Mendapatkan info user yang sedang terautentikasi
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'nim_nik' => $user->nim_nik,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'role' => $user->role ? $user->role->kode_role : null,
                'foto_url' => $user->foto_url,
            ],
        ], 200);
    }
}
