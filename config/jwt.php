<?php

return [
    'secret' => env('JWT_SECRET', 'your-super-secret-jwt-key'),
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
    'ttl' => env('JWT_TTL', 60),
];
