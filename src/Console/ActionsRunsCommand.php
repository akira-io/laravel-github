<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Console\Command;

/**
 * Artisan command to list recent Actions workflow runs.
 */
final class ActionsRunsCommand extends Command
{
    protected $signature = 'github:actions:runs {owner} {repo} {--per_page=10}';

    protected $description = 'List recent GitHub Actions workflow runs';

    /**
     * Execute the console command.
     */
    public function handle(GitHubManager $github): int
    {
        $owner = (string) $this->argument('owner');
        $repo = (string) $this->argument('repo');
        $per = (int) $this->option('per_page');

        $runs = $github->actionsWorkflowRuns($owner, $repo, ['per_page' => $per]);
        $rows = $runs->map(fn ($r) => [$r->id, $r->name, $r->status, (string) $r->conclusion])->all();
        $this->table(['ID', 'Name', 'Status', 'Conclusion'], $rows);

        return self::SUCCESS;
    }
}
