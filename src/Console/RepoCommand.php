<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Console\Command;

/**
 * Artisan command to display repository details.
 */
final class RepoCommand extends Command
{
    protected $signature = 'github:repo {owner} {repo}';

    protected $description = 'Show GitHub repository information';

    /**
     * Execute the console command.
     */
    public function handle(GitHubManager $github): int
    {
        $data = $github->repo((string) $this->argument('owner'), (string) $this->argument('repo'));
        $this->table(['id', 'full_name', 'stars', 'forks'], [[$data->id, $data->full_name, (string) $data->stargazers_count, (string) $data->forks_count]]);

        return self::SUCCESS;
    }
}
