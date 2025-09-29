# Advanced Topics

## Rate Limiting

All write operations are wrapped with an application-side rate limiter to avoid hitting GitHub abuse detection.

## Events

- `Akira\\GitHub\\Events\\RequestSending` is fired before a REST request.
- `Akira\\GitHub\\Events\\RateLimited` is fired when the custom limiter blocks a call.

## Testing

The package ships with `FakeRest` and `StubGithubClient` for predictable testing without hitting real GitHub APIs.
