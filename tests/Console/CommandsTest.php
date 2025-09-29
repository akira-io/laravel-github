<?php

declare(strict_types=1);

use Akira\GitHub\Services\GitHubManager;
use Akira\GitHub\Tests\Support\FakeRest;
use Akira\GitHub\Tests\Support\StubGithubClient;
use Akira\GitHub\Tests\TestCase;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

uses(TestCase::class);
beforeEach(function () {
    $this->app->instance(GitHubManager::class, tap(new GitHubManager(new StubGithubClient([
        'user.show' => ['octo' => ['login' => 'octo', 'name' => 'Octo', 'followers' => 1]],
        'repo.show' => ['akira/hunter' => ['id' => 1, 'name' => 'hunter', 'full_name' => 'akira/hunter', 'private' => false]],
        'issue.all' => ['akira/hunter' => [['number' => 1, 'title' => 'Bug', 'state' => 'open']]],
        'pr.all' => ['akira/hunter' => [['number' => 2, 'title' => 'PR', 'state' => 'open']]],
    ]), new Repository(new ArrayStore()), ['cache' => ['ttl' => 0, 'prefix' => 'g:'], 'events' => false]), function (GitHubManager $m) {
        $m->setRest(new FakeRest()->when('GET /repos/akira/hunter/actions/runs', ['workflow_runs' => [['id' => 1, 'name' => 'ci', 'status' => 'completed']]]));
    }));
});

it('executes user command', function () {
    $this->artisan('github:user', ['username' => 'octo'])->assertExitCode(0);
});
it('executes repo command', function () {
    $this->artisan('github:repo', ['owner' => 'akira', 'repo' => 'hunter'])->assertExitCode(0);
});
it('executes issue list command', function () {
    $this->artisan('github:issue:list', ['owner' => 'akira', 'repo' => 'hunter'])->assertExitCode(0);
});
it('executes pr list command', function () {
    $this->artisan('github:pr:list', ['owner' => 'akira', 'repo' => 'hunter'])->assertExitCode(0);
});
it('executes actions runs command', function () {
    $this->artisan('github:actions:runs', ['owner' => 'akira', 'repo' => 'hunter', '--per_page' => 5])->assertExitCode(0);
});
