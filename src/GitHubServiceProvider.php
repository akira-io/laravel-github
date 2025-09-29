<?php

declare(strict_types=1);

namespace Akira\GitHub;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Akira GitHub package.
 *
 * Registers the GitHubManager singleton, wires authentication (token or GitHub App),
 * and exposes console commands and configuration publishing.
 */
final class GitHubServiceProvider extends ServiceProvider
{
    /**
     * Register the package services and merge configuration.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/github.php', 'github');

    }

    /**
     * Bootstrap the package: register Artisan commands and publish assets.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\UserCommand::class,
                Console\RepoCommand::class,
                Console\IssueListCommand::class,
                Console\PrListCommand::class,
                Console\ActionsRunsCommand::class,
                Console\MakeWebhookControllerCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/github.php' => config_path('github.php'),
        ], 'config');
    }
}
