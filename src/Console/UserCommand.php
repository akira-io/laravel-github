<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Console\Command;

/**
 * Artisan command to display GitHub user details.
 */
final class UserCommand extends Command
{
    protected $signature = 'github:user {username}';

    protected $description = 'Show GitHub user information';

    /**
     * Execute the console command.
     */
    public function handle(GitHubManager $github): int
    {
        $user = $github->user((string) $this->argument('username'));
        $this->table(['login', 'name', 'followers'], [[$user->login, $user->name, (string) $user->followers]]);

        return self::SUCCESS;
    }
}
