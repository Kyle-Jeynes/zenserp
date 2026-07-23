<?php

return [
    'api_key' => env('ZENSERP_API_KEY', ''),

    'cache' => [
        'results_limit' => (int) env('ZENSERP_CACHE_RESULTS_LIMIT', 20),
    ],

    'stream' => [
        'preload_count' => (int) env('ZENSERP_STREAM_PRELOAD_COUNT', 1),
    ],

    'rate_limit' => [
        'limiter' => env('ZENSERP_RATE_LIMITER', 'zenserp'),
        'max_attempts' => (int) env('ZENSERP_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_seconds' => (int) env('ZENSERP_RATE_LIMIT_DECAY_SECONDS', 60),
    ],
];
