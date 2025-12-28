<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    */

    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    // Set to true for production, false for sandbox
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Set to true to enable 3DS for credit card transactions
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    // Sanitize request data
    'is_sanitized' => true,
];
