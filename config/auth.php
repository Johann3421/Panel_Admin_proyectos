<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'app_users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'app_users', // Asegúrate de que el proveedor coincida
        ],
    ],

    'providers' => [
        'app_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AppUser::class, // Modelo que creaste para la tabla app_users
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
