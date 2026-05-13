<?php

return [

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        // Default web guard (Laravel internals)
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Super Admin guard — reads from public.super_admins table
        'super_admin' => [
            'driver'   => 'session',
            'provider' => 'super_admins',
        ],

        // NOTE: No 'tenant' guard — tenant workspaces have no login
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\SuperAdmin::class, // fallback
        ],
        'super_admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\SuperAdmin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'super_admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
