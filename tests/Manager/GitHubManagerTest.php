<?php

declare(strict_types=1);

use Akira\GitHub\Services\GitHubManager;
use Akira\GitHub\Tests\Support\FakeRest;
use Akira\GitHub\Tests\Support\StubGithubClient;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

it('fetches user and repos via client sub-apis', function () {
    $client = new StubGithubClient([
        'user.show' => ['octocat' => ['login' => 'octocat', 'name' => 'The Octocat', 'followers' => 10]],
        'user.repositories' => ['octocat' => [1 => [30 => [['id' => 1, 'name' => 'repo', 'full_name' => 'octocat/repo', 'private' => false]]]]],
        'repo.show' => ['akira/hunter' => ['id' => 99, 'name' => 'hunter', 'full_name' => 'akira/hunter', 'private' => false]],
    ]);
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, ['cache' => ['ttl' => 60, 'prefix' => 'g:'], 'pagination' => ['per_page' => 30]]);

    $u = $m->user('octocat');
    expect($u->login)->toBe('octocat');

    $repos = $m->userRepos('octocat');
    expect($repos)->toHaveCount(1);

    $r = $m->repo('akira', 'hunter');
    expect($r->id)->toBe(99);
});

it('lists issues, pulls, releases and gists/org/teams', function () {
    $client = new StubGithubClient([
        'issue.all' => ['akira/hunter' => [['number' => 1, 'title' => 'Bug', 'state' => 'open']]],
        'pr.all' => ['akira/hunter' => [['number' => 2, 'title' => 'PR', 'state' => 'open']]],
        'repo.releases' => ['akira/hunter' => [['id' => 1, 'tag_name' => 'v1']]],
        'org.show' => ['akira-io' => ['login' => 'akira-io', 'name' => 'Akira']],
        'org.repositories' => ['akira-io' => [['id' => 1, 'name' => 'x', 'full_name' => 'akira-io/x', 'private' => false]]],
        'team.all' => ['akira-io' => [['name' => 'Core', 'slug' => 'core']]],
        'gist.all' => ['octo' => [['id' => 'g1']]],
        'gist.show' => ['g1' => ['id' => 'g1']],
    ]);
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, ['cache' => ['ttl' => 60, 'prefix' => 'g:']]);
    expect($m->issues('akira', 'hunter'))->toHaveCount(1)
        ->and($m->pulls('akira', 'hunter'))->toHaveCount(1)
        ->and($m->releases('akira', 'hunter'))->toHaveCount(1)
        ->and($m->organization('akira-io')->login)->toBe('akira-io')
        ->and($m->orgRepos('akira-io'))->toHaveCount(1)
        ->and($m->teams('akira-io'))->toHaveCount(1)
        ->and($m->gists('octo'))->toHaveCount(1)
        ->and($m->gist('g1')->id)->toBe('g1');
});

it('creates and comments issues', function () {
    $client = new StubGithubClient();
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, ['cache' => ['ttl' => 0, 'prefix' => 'g:']]);
    $issue = $m->createIssue('akira', 'hunter', 'Title', 'Body', ['labels' => ['bug']]);
    expect($issue->title)->toBe('Title');
    $resp = $m->commentOnIssue('akira', 'hunter', 1, 'Hey');
    expect($resp['body'])->toBe('Hey');
});

it('covers actions/checks/packages/dependabot via FakeRest seam', function () {
    $client = new StubGithubClient();
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, [
        'cache' => ['ttl' => 0, 'prefix' => 'g:'],
        'events' => false,
    ]);

    $fake = new FakeRest();
    $fake->when('GET /repos/akira/hunter/actions/runs', [
        'workflow_runs' => [['id' => 1, 'name' => 'ci', 'status' => 'completed']],
    ]);
    $fake->when('GET /repos/akira/hunter/commits/sha/check-runs', [
        'check_runs' => [['id' => 1, 'name' => 'lint', 'status' => 'completed']],
    ]);
    $fake->when('GET /orgs/akira-io/packages?package_type=container', [
        ['name' => 'img', 'package_type' => 'container'],
    ]);
    $fake->when('GET /repos/akira/hunter/dependabot/alerts', [
        ['number' => 1, 'state' => 'open'],
    ]);

    $m->setRest($fake);

    expect($m->actionsWorkflowRuns('akira', 'hunter'))->toHaveCount(1)
        ->and($m->checksForRef('akira', 'hunter', 'sha'))->toHaveCount(1)
        ->and($m->orgPackages('akira-io'))->toHaveCount(1)
        ->and($m->dependabotAlerts('akira', 'hunter'))->toHaveCount(1);
});

it('downloads artifact, reruns and cancels via FakeRest', function () {
    $client = new StubGithubClient();
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, ['cache' => ['ttl' => 0, 'prefix' => 'g:'], 'events' => false]);
    $fake = new FakeRest();
    $m->setRest($fake);

    $dest = sys_get_temp_dir().'/artifact.zip';
    $path = $m->actionsDownloadArtifact('akira', 'hunter', 123, $dest);
    expect(file_exists($path))->toBeTrue()
        ->and($m->actionsRerun('akira', 'hunter', 1))->toBeArray()
        ->and($m->actionsCancel('akira', 'hunter', 1))->toBeArray();
});

it('executes graphql and verifies webhook signature', function () {
    $client = new StubGithubClient();
    $cache = new Repository(new ArrayStore());
    $m = new GitHubManager($client, $cache, [
        'cache' => ['ttl' => 0, 'prefix' => 'g:'],
        'graphql_fake' => ['data' => ['__typename' => 'Query']], // inject fake response
    ]);

    $result = $m->graphql('query{__typename}');
    expect($result)->toBeArray()
        ->and($result['data']['__typename'])->toBe('Query');

    $payload = '{"a":1}';
    $secret = 's';
    $sig = 'sha256='.hash_hmac('sha256', $payload, $secret);
    expect($m->verifyWebhookSignature($secret, $payload, $sig))->toBeTrue();
});
