# Artisan Commands

The package ships with commands for common GitHub operations.

```bash
php artisan github:user octocat
php artisan github:repo akira hunter
php artisan github:issue:list akira hunter
php artisan github:pr:list akira hunter
php artisan github:actions:runs akira hunter --per_page=5
```
