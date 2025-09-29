# Advanced Topics

## Rate Limiting
All write operations are wrapped in an application-side rate limiter to avoid hitting GitHub API abuse detection.

## Events
- `RequestSending`: Fired before a REST request is sent.
- `RateLimited`: Fired when the custom limiter blocks a call.

## Testing
The package ships with:
- `FakeRest`: Simulates GitHub REST endpoints.
- `StubGithubClient`: Simulates client sub-APIs.

This enables full test coverage without accessing the real GitHub API.
