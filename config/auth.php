<?php
    return [
        'defaults' => [
            'guard' => 'api',
        ],
        'guards' => [
            'api' => [
                'driver' => 'jwt',
                'audience' => explode(',', env('JWT_TOKEN_AUDIENCE')),
                'keys' => [
                    0 => [ 'path' => env('JWT_PUBLIC_KEY', 'keys/public'), 'algorithm' => 'RS256' ],
                ]
            ],
        ],
    ];
