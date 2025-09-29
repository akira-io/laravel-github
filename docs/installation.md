# Installation

Require the package via Composer:

```bash
composer require akira/laravel-github
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=config --provider="Akira\\GitHub\\GitHubServiceProvider"
```

This will create a `config/github.php` file.

Optionally, run migrations in case future versions include database tables for GitHub sync:

```bash
php artisan migrate
```
