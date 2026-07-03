<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized admin settings to avoid hardcoded references.
    |
    */

    'user_id' => env('ADMIN_USER_ID', 1),

    'name' => env('ADMIN_NAME', 'Lakshya Admin'),

    'email' => env('ADMIN_EMAIL', 'admin@lakshya.ai'),

    /*
    |--------------------------------------------------------------------------
    | VM Agent Configuration
    |--------------------------------------------------------------------------
    |
    | Python VM scraper agent connection settings.
    |
    */
    'vm' => [
        'base_url' => env('VM_AGENT_LIVE', false) ? env('VM_AGENT_URL') : 'http://127.0.0.1:5000',
        'timeout' => (int) env('VM_AGENT_TIMEOUT', 60),
        'status_timeout' => (int) env('VM_AGENT_STATUS_TIMEOUT', 1),
    ],
];