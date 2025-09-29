# Laravel GitHub

A Laravel 12+ package to interact with the GitHub REST and GraphQL APIs using a strongly-typed, collection-friendly
interface.

This package provides:

- Fully typed DTOs for common GitHub resources (users, repos, issues, PRs, actions, packages, dependabot, etc.).
- High-level service `GitHubManager` with caching and rate limiting.
- Artisan commands to quickly query GitHub resources.
- 100% test coverage with Pest and static analysis with Larastan.

---

## Installation

```bash
composer require akira/laravel-github
php artisan vendor:publish --tag=config --provider="Akira\\GitHub\\GitHubServiceProvider"
```

---

## Configuration

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
    'pagination' => ['per_page' => 30],
    'rate_limiter' => ['max' => 60, 'decay_seconds' => 60],
    'events' => true,
];
```

---

## Usage Examples

### Users

```php
$user = GitHub::user('octocat');
$repos = GitHub::userRepos('octocat');
```

### Repositories

```php
$repo = GitHub::repo('akira', 'hunter');
```

### Issues

```php
$issues = GitHub::issues('akira', 'hunter');
$new = GitHub::createIssue('akira', 'hunter', 'Bug', 'Description');
GitHub::commentOnIssue('akira', 'hunter', $new->number, 'Working on it!');
```

### Pull Requests

```php
$prs = GitHub::pulls('akira', 'hunter');
```

### Releases

```php
$releases = GitHub::releases('akira', 'hunter');
```

### Organizations and Teams

```php
$org = GitHub::organization('akira-io');
$repos = GitHub::orgRepos('akira-io');
$teams = GitHub::teams('akira-io');
```

### Gists

```php
$gists = GitHub::gists('octocat');
$gist = GitHub::gist('123');
```

### GitHub Actions

```php
$runs = GitHub::actionsWorkflowRuns('akira', 'hunter');
GitHub::actionsRerun('akira', 'hunter', $runs[0]->id);
GitHub::actionsCancel('akira', 'hunter', $runs[0]->id);
GitHub::actionsDownloadArtifact('akira', 'hunter', $runs[0]->id, storage_path('artifact.zip'));
```

### Checks

```php
$checks = GitHub::checksForRef('akira', 'hunter', 'main');
```

### Packages

```php
$packages = GitHub::orgPackages('akira-io', 'container');
```

### Dependabot Alerts

```php
$alerts = GitHub::dependabotAlerts('akira', 'hunter');
```

### Projects V2 (GraphQL)

```php
$projects = GitHub::projectsV2('akira');
```

### Webhook Verification

```php
$isValid = GitHub::verifyWebhookSignature($secret, $payload, $signature);
```

---

## Artisan Commands

```bash
php artisan github:user octocat
php artisan github:repo akira hunter
php artisan github:issue:list akira hunter
php artisan github:pr:list akira hunter
php artisan github:actions:runs akira hunter --per_page=5
```

---

## 📚 Documentation

This package also includes a dedicated `docs/` folder with extended details:

- Installation — [docs/installation.md](docs/installation.md)
- Configuration — [docs/configuration.md](docs/configuration.md)
- Usage — [docs/usage.md](docs/usage.md)
- Commands — [docs/commands.md](docs/commands.md)
- Advanced Topics — [docs/advanced.md](docs/advanced.md)
- Contributing — [docs/contributing.md](docs/contributing.md)
- Roadmap — [docs/roadmap.md](docs/roadmap.md)

---

## Project Structure

```
laravel-github/
├── src/                # Package source code
├── tests/              # Pest test suite
├── docs/               # Extended documentation
├── README.md           # Quick start & inline docs
├── composer.json
└── pest.php / phpunit.xml
```

---

## Contributing

1. Fork the repo
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Run tests (`composer test`)
4. Ensure code style and static analysis pass (`composer analyse`)
5. Submit a PR

---

## License

MIT
