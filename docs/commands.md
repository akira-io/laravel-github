# Artisan Commands

The package provides Artisan commands for quick access to GitHub data.

## github:user {username}
Fetches details of a GitHub user.

Example:
```bash
php artisan github:user octocat
```

## github:repo {owner} {repo}
Fetches details of a repository.

Example:
```bash
php artisan github:repo akira hunter
```

## github:issue:list {owner} {repo}
Lists issues of a repository.

Example:
```bash
php artisan github:issue:list akira hunter
```

## github:pr:list {owner} {repo}
Lists pull requests of a repository.

Example:
```bash
php artisan github:pr:list akira hunter
```

## github:actions:runs {owner} {repo} [--per_page=N]
Lists workflow runs of a repository.

Example:
```bash
php artisan github:actions:runs akira hunter --per_page=5
```
