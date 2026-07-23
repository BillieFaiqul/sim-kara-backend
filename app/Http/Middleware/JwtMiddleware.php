<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
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

            $request->user = $payload;

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }
    }
}
