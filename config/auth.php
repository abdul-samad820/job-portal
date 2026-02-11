<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        // Default user guard
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

      
        'admin' => [
            'driver' => 'session', 
            'provider' => 'admins',
        ],

     
        'superadmin' => [
    'driver' => 'session',
    'provider' => 'superadmins',
],


       
        'user' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],


        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
      
        'superadmins' => [
    'driver' => 'eloquent',
    'model' => App\Models\Admin::class,
],
 
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    // 'passwords' => [
    //     'users' => [
    //         'provider' => 'users',
    //         'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
    //         'expire' => 10,
    //         'throttle' => 10,
    //     ],

    //     // Optional: if you want admin password resets separately
    //     'admins' => [
    //         'provider' => 'admins',
    //         'table' => 'admin_password_reset_tokens',
    //         'expire' => 10,
    //         'throttle' => 10,
    //     ],
    // ],

    // 'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
