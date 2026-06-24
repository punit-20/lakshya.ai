<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized pricing and billing settings.
    |
    */

    'currency' => env('PRICING_CURRENCY', 'INR'),

    'tiers' => [
        'free_trial' => [
            'name' => 'Free Trial',
            'credits' => 10,
            'duration_days' => 14,
        ],
        'starter' => [
            'name' => 'Starter',
            'price' => (int) env('STARTER_PRICE', 1499),
            'credits' => 50,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => (int) env('PRO_PRICE', 4999),
            'credits' => 200,
        ],
    ],

    'average_price' => (int) env('AVERAGE_PRICE', 3249),

    'nsfw_penalty_credits' => (int) env('NSFW_PENALTY_CREDITS', 5),

    'opex' => [
        'customer_care_salary' => (int) env('OPEX_CUSTOMER_CARE', 8000),
        'acquisition_ad_spend' => (int) env('OPEX_AD_SPEND', 5000),
        'vm_crawler_instances' => (int) env('OPEX_VM_CRAWLER', 2500),
        'web_hosting' => (int) env('OPEX_HOSTING', 1500),
        'gemini_api_fees' => (int) env('OPEX_GEMINI_API', 1000),
    ],

    'targets' => [
        'clients' => (int) env('TARGET_CLIENTS', 15),
        'mrr' => (int) env('TARGET_MRR', 38000),
        'net_profit' => (int) env('TARGET_NET_PROFIT', 20000),
        'margin' => (int) env('TARGET_MARGIN', 50),
    ],
];