# Configuration

The configuration file looks like this:

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

### Options explained

- **token**: GitHub Personal Access Token. Required for private repositories or higher rate limits.
- **cache**: Controls caching of API calls to reduce requests to GitHub.
- **pagination**: Default items per page for list endpoints.
- **rate_limiter**: Ensures the package does not exceed configured limits locally.
- **events**: When true, emits events like `RequestSending` and `RateLimited`.
