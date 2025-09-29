<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Console\Command;

/**
 * Artisan command to list pull requests for a repository.
 */
final class PrListCommand extends Command
{
    protected $signature = 'github:pr:list {owner} {repo}';

    protected $description = 'List pull requests for a repository';

    /**
     * Execute the console command.
     */
    public function handle(GitHubManager $github): int
    {
        $prs = $github->pulls((string) $this->argument('owner'), (string) $this->argument('repo'));
        $rows = $prs->map(fn ($p) => [$p->number, $p->title, $p->state])->all();
        $this->table(['#', 'Title', 'State'], $rows);

        return self::SUCCESS;
    }
}
