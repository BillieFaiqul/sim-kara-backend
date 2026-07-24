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
        $menuItems = $this->getMenuByRole($user->role);

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
            'menu_items' => $menuItems,
        ], 200);
    }

    private function getMenuByRole(string $role): array
    {
        $baseMenu = [
            [
                'label' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'Home',
            ],
            [
                'label' => 'Semua Karya',
                'href' => '/semua-karya',
                'icon' => 'FileText',
            ],
        ];

        $roleSpecificMenu = match($role) {
            'admin' => [
                [
                    'label' => 'Validasi',
                    'href' => '/validasi',
                    'icon' => 'CheckSquare',
                    'submenu' => [
                        [
                            'label' => 'Validasi Pending',
                            'href' => '/validasi/pending',
                            'icon' => 'Clock',
                        ],
                        [
                            'label' => 'Riwayat Validasi',
                            'href' => '/validasi/riwayat',
                            'icon' => 'History',
                        ],
                    ],
                ],
                [
                    'label' => 'Kelola User',
                    'href' => '/kelola-user',
                    'icon' => 'Users',
                ],
            ],
            'dosen' => [
                [
                    'label' => 'Karya Saya',
                    'href' => '/karya-saya',
                    'icon' => 'Upload',
                ],
            ],
            'mahasiswa' => [
                [
                    'label' => 'Karya Saya',
                    'href' => '/karya-saya',
                    'icon' => 'Upload',
                ],
            ],
            default => [],
        };

        return array_merge($baseMenu, $roleSpecificMenu);
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
