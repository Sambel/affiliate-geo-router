<?php

return [
    'default_country' => env('DEFAULT_COUNTRY', 'FR'),
    'click_log_retention_days' => env('CLICK_LOG_RETENTION_DAYS', 180),
    'rate_limit_per_minute' => env('RATE_LIMIT_PER_MINUTE', 100),
    'maxmind_license_key' => env('MAXMIND_LICENSE_KEY'),
    'maxmind_user_id' => env('MAXMIND_USER_ID'),
];