<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| GitHub Package Configuration
|--------------------------------------------------------------------------
| This file defines all configuration options for the Akira GitHub package.
| Each section includes a description in Laravel's configuration style.
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Default Authentication Method
    |--------------------------------------------------------------------------
    | Controls how the GitHub client authenticates outgoing requests.
    | Supported values:
    |   - "token": Personal access token (default).
    |   - "app":   GitHub App (installation token).
    |   - "none":  No authentication (public endpoints only).
    */

    'auth' => env('GITHUB_AUTH', 'token'),

    /*
    |--------------------------------------------------------------------------
    | Personal Access Token
    |--------------------------------------------------------------------------
    | Fine‑grained token used when 'auth' is set to 'token'. Provide the
    | appropriate scopes for the endpoints your app calls.
    */

    'token' => env('GITHUB_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | GitHub App Credentials
    |--------------------------------------------------------------------------
    | Used when 'auth' is set to 'app'. The private key must be in PEM format.
    */

    'app' => [
        'id' => env('GITHUB_APP_ID'),
        'installation_id' => env('GITHUB_APP_INSTALLATION_ID'),
        'private_key' => env('GITHUB_APP_PRIVATE_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub Enterprise Base URL
    |--------------------------------------------------------------------------
    | Set your GHE Server URL if applicable; leave null for public GitHub.
    */

    'enterprise_url' => env('GITHUB_ENTERPRISE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Response Caching
    |--------------------------------------------------------------------------
    | Configure cache store, default TTL and per-endpoint TTL overrides.
    */

    'cache' => [
        'store' => env('GITHUB_CACHE_STORE', env('CACHE_STORE', 'file')),
        'ttl' => env('GITHUB_CACHE_TTL', 300), // default seconds
        'prefix' => 'github:',
        'ttls' => [
            'users' => 600,
            'repos' => 300,
            'issues' => 120,
            'pulls' => 120,
            'releases' => 600,
            'actions' => 60,
            'checks' => 60,
            'projects' => 300,
            'packages' => 600,
            'dependabot' => 300,
            'gists' => 300,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Simple Rate Limiting
    |--------------------------------------------------------------------------
    | Cache-backed throttling to avoid hammering the API from your app.
    */

    'rate_limiter' => [
        'max' => env('GITHUB_RATE_MAX', 60),
        'decay_seconds' => env('GITHUB_RATE_DECAY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Defaults
    |--------------------------------------------------------------------------
    | Default page size for list endpoints when perPage is not supplied.
    */

    'pagination' => [
        'per_page' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug & Events
    |--------------------------------------------------------------------------
    | - debug: enable richer HTTP exceptions
    | - events: emit RequestSending / ResponseReceived / RateLimited
    */

    'debug' => env('GITHUB_DEBUG', false),
    'events' => env('GITHUB_EVENTS', true),
];
