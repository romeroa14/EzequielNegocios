<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Google Analytics settings.
    |
    */

    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_ID', 'G-C4HS1YLDQ3'),
        'enabled' => env('GOOGLE_ANALYTICS_ENABLED', true),
    ],

    'google_tag_manager' => [
        'container_id' => env('GOOGLE_TAG_MANAGER_ID', 'GTM-MCC8RRTT'),
        'enabled' => env('GOOGLE_TAG_MANAGER_ENABLED', true),
    ],
];
