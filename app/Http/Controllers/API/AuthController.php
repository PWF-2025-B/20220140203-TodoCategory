<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Login user dengan email dan password.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Attempt login dan buat token JWT
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Email atau password salah',
                ], 401);
            }

            $user = auth()->user();

            return response()->json([
                'status_code' => 200,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                    ],
                    'token' => $token,
                ],
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Tidak bisa membuat token',
            ], 500);
        }
    }

    /**
     * Logout user yang sedang login.
     * 
     * Menghapus token JWT agar tidak bisa digunakan lagi
     */

     #[Response(
        status: 200,
        content: [
            'status_code' => 200,
            'message' => 'Logout berhasil. Token telah dihapus.'
        ]
     )]

     #[Response(
        status: 500,
        content: [
            'status_code' => 500,
            'message' => 'Gagal logout, terjadi kesalahan.'
        ]
     )]

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status_code' => 200,
                'message' => 'Logout berhasil. Token telah dihapus.',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Gagal logout, terjadi kesalahan.',
            ], 500);
        }
    }
}
