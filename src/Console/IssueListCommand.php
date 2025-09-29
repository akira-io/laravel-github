<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Console\Command;

/**
 * Artisan command to list issues for a repository.
 */
final class IssueListCommand extends Command
{
    protected $signature = 'github:issue:list {owner} {repo}';

    protected $description = 'List issues for a repository';

    /**
     * Execute the console command.
     */
    public function handle(GitHubManager $github): int
    {
        $issues = $github->issues((string) $this->argument('owner'), (string) $this->argument('repo'));
        $rows = $issues->map(fn ($i) => [$i->number, $i->title, $i->state])->all();
        $this->table(['#', 'Title', 'State'], $rows);

        return self::SUCCESS;
    }
}
