<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    private function generateJWT(User $user): string
    {
        $now = time();
        $expire = $now + (config('jwt.ttl', 60) * 60);

        $payload = [
            'iat' => $now,
            'exp' => $expire,
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
        ];

        return JWT::encode(
            $payload,
            config('jwt.secret'),
            config('jwt.algorithm', 'HS256')
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive',
            ], 403);
        }

        $token = $this->generateJWT($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    public function refresh(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided',
                ], 401);
            }

            $payload = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algorithm', 'HS256')));

            $user = User::find($payload->id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $newToken = $this->generateJWT($user);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed',
                'token' => $newToken,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }
    }

    public function me(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }
}
