# Configuration

The configuration file `config/github.php` includes:

```php
return [
    'token' => env('GITHUB_TOKEN', null),

    'cache' => [
        'ttl' => 300,
        'prefix' => 'github:',
        'ttls' => [
            'issues' => 60,
            'pulls' => 60,
            'releases' => 300,
        ],
    ],

    'pagination' => [
        'per_page' => 30,
    ],

    'rate_limiter' => [
        'max' => 60,
        'decay_seconds' => 60,
    ],

    'events' => true,
];
```

- **token** — your GitHub personal access token.
- **cache** — control caching of API calls.
- **pagination** — default items per page.
- **rate_limiter** — protects against abuse.
- **events** — whether to emit `RequestSending` and `RateLimited` events.
