# Installation

Require the package:

```bash
composer require akira/laravel-github
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=config --provider="Akira\\GitHub\\GitHubServiceProvider"
```

Run migrations if needed (future features may include GitHub sync tables):

```bash
php artisan migrate
```
