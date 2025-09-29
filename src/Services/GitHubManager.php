<?php

declare(strict_types=1);

namespace Akira\GitHub\Services;

use Akira\GitHub\DTO\ActionsRunDTO;
use Akira\GitHub\DTO\CheckRunDTO;
use Akira\GitHub\DTO\DependabotAlertDTO;
use Akira\GitHub\DTO\GistDTO;
use Akira\GitHub\DTO\IssueDTO;
use Akira\GitHub\DTO\OrganizationDTO;
use Akira\GitHub\DTO\PackageDTO;
use Akira\GitHub\DTO\ProjectV2DTO;
use Akira\GitHub\DTO\PullRequestDTO;
use Akira\GitHub\DTO\ReleaseDTO;
use Akira\GitHub\DTO\RepoDTO;
use Akira\GitHub\DTO\TeamDTO;
use Akira\GitHub\DTO\UserDTO;
use Akira\GitHub\Events\RequestSending;
use Akira\GitHub\Http\Rest;
use Akira\GitHub\Support\RateLimiter;
use Github\Client;
use Http\Discovery\Psr17FactoryDiscovery;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * High-level service for interacting with GitHub's REST and GraphQL APIs.
 *
 * This manager provides typed accessors (DTOs) for common GitHub resources
 * and handles caching, rate limiting, and event emission.
 */
final class GitHubManager
{
    /**
     * Optional override of the REST layer (mainly for testing).
     */
    private ?object $restOverride = null;

    public function __construct(
        private Client $client,
        private CacheRepository $cache,
        /** @var array<string,mixed> */
        private array $config,
        private bool $events = true,
    ) {}

    /**
     * Get a GitHub user by username.
     */
    public function user(string $username): UserDTO
    {
        $data = $this->cacheRemember("users:$username", fn () => $this->client->user()->show($username));

        return UserDTO::fromArray($data);
    }

    /**
     * List repositories for a given user.
     *
     * @return Collection<int, RepoDTO>
     */
    public function userRepos(string $username, int $page = 1, ?int $perPage = null): Collection
    {
        $perPage ??= (int) Arr::get($this->config, 'pagination.per_page', 30);
        $repos = $this->cacheRemember(
            "users:$username:repos:$page:$perPage",
            fn () => $this->client->user()->repositories($username, 'owner', 'full_name', 'desc', $page, $perPage)
        );

        return collect($repos)->map(fn (array $repo) => RepoDTO::fromArray($repo));
    }

    /**
     * Get a single repository by owner/name.
     */
    public function repo(string $owner, string $repo): RepoDTO
    {
        $data = $this->cacheRemember("repos:$owner/$repo", fn () => $this->client->repo()->show($owner, $repo));

        return RepoDTO::fromArray($data);
    }

    /**
     * List issues for a repository.
     *
     * @return Collection<int, IssueDTO>
     */
    public function issues(string $owner, string $repo, array $params = []): Collection
    {
        $data = $this->cacheRememberWithTtlOverride(
            'issues',
            "issues:$owner/$repo:".md5(json_encode($params)),
            fn () => $this->client->issue()->all($owner, $repo, $params)
        );

        return collect($data)->map(fn (array $i) => IssueDTO::fromArray($i));
    }

    /**
     * List pull requests for a repository.
     *
     * @return Collection<int, PullRequestDTO>
     */
    public function pulls(string $owner, string $repo, array $params = []): Collection
    {
        $data = $this->cacheRememberWithTtlOverride(
            'pulls',
            "pulls:$owner/$repo:".md5(json_encode($params)),
            fn () => $this->client->pullRequest()->all($owner, $repo, $params)
        );

        return collect($data)->map(fn (array $p) => PullRequestDTO::fromArray($p));
    }

    /**
     * List releases for a repository.
     *
     * @return Collection<int, ReleaseDTO>
     */
    public function releases(string $owner, string $repo): Collection
    {
        $data = $this->cacheRememberWithTtlOverride(
            'releases',
            "releases:$owner/$repo",
            fn () => $this->client->repo()->releases()->all($owner, $repo)
        );

        return collect($data)->map(fn (array $r) => ReleaseDTO::fromArray($r));
    }

    /**
     * Create an issue in a repository.
     */
    public function createIssue(string $owner, string $repo, string $title, ?string $body = null, array $extra = []): IssueDTO
    {
        $payload = array_filter(array_merge(['title' => $title, 'body' => $body], $extra), static fn ($v) => $v !== null);
        $data = $this->rateLimited("issues:create:$owner/$repo", fn () => $this->client->issue()->create($owner, $repo, $payload));

        return IssueDTO::fromArray($data);
    }

    /**
     * Comment on an existing issue.
     *
     * @return array<string,mixed>
     */
    public function commentOnIssue(string $owner, string $repo, int $issueNumber, string $comment): array
    {
        return $this->rateLimited(
            "issues:comment:$owner/$repo",
            fn () => $this->client->issue()->comments()->create($owner, $repo, $issueNumber, ['body' => $comment])
        );
    }

    /**
     * Get organization details.
     */
    public function organization(string $org): OrganizationDTO
    {
        $data = $this->cacheRemember("org:$org", fn () => $this->client->organization()->show($org));

        return OrganizationDTO::fromArray($data);
    }

    /**
     * List repositories for an organization.
     *
     * @return Collection<int, RepoDTO>
     */
    public function orgRepos(string $org, array $params = []): Collection
    {
        $data = $this->cacheRemember(
            "org:$org:repos:".md5(json_encode($params)),
            fn () => $this->client->organization()->repositories($org, $params)
        );

        return collect($data)->map(fn (array $r) => RepoDTO::fromArray($r));
    }

    /**
     * List teams for an organization.
     *
     * @return Collection<int, TeamDTO>
     */
    public function teams(string $org): Collection
    {
        $data = $this->cacheRemember("org:$org:teams", fn () => $this->client->team()->all($org));

        return collect($data)->map(fn (array $t) => TeamDTO::fromArray($t));
    }

    /**
     * List gists for a user.
     *
     * @return Collection<int, GistDTO>
     */
    public function gists(string $username, array $params = []): Collection
    {
        $data = $this->cacheRemember("gists:$username:".md5(json_encode($params)), fn () => $this->client->gist()->all($username, $params));

        return collect($data)->map(fn (array $g) => GistDTO::fromArray($g));
    }

    /**
     * Get a single gist by id.
     */
    public function gist(string $id): GistDTO
    {
        $data = $this->cacheRemember("gist:$id", fn () => $this->client->gist()->show($id));

        return GistDTO::fromArray($data);
    }

    /**
     * List workflow runs for a repository (GitHub Actions).
     *
     * @return Collection<int, ActionsRunDTO>
     */
    public function actionsWorkflowRuns(string $owner, string $repo, array $params = []): Collection
    {
        $query = $params ? ('?'.http_build_query($params)) : '';
        $data = $this->cacheRememberWithTtlOverride(
            'actions',
            "actions:runs:$owner/$repo:".md5($query),
            fn () => $this->rest()->get("/repos/{$owner}/{$repo}/actions/runs{$query}")
        );
        $runs = $data['workflow_runs'] ?? [];

        return collect($runs)->map(fn (array $r) => ActionsRunDTO::fromArray($r));
    }

    /**
     * List check runs for a commit ref.
     *
     * @return Collection<int, CheckRunDTO>
     */
    public function checksForRef(string $owner, string $repo, string $ref, array $params = []): Collection
    {
        $query = $params ? ('?'.http_build_query($params)) : '';
        $data = $this->cacheRememberWithTtlOverride(
            'checks',
            "checks:$owner/$repo:$ref:".md5($query),
            fn () => $this->rest()->get("/repos/{$owner}/{$repo}/commits/{$ref}/check-runs{$query}")
        );
        $runs = $data['check_runs'] ?? [];

        return collect($runs)->map(fn (array $r) => CheckRunDTO::fromArray($r));
    }

    /**
     * Download an Actions artifact as a ZIP file.
     */
    public function actionsDownloadArtifact(string $owner, string $repo, int $artifactId, string $destPath): string
    {
        return $this->rest()->download("/repos/{$owner}/{$repo}/actions/artifacts/{$artifactId}/zip", $destPath);
    }

    /**
     * Rerun a workflow run.
     *
     * @return array<string,mixed>
     */
    public function actionsRerun(string $owner, string $repo, int $runId): array
    {
        return $this->rest()->post("/repos/{$owner}/{$repo}/actions/runs/{$runId}/rerun");
    }

    /**
     * Cancel a workflow run.
     *
     * @return array<string,mixed>
     */
    public function actionsCancel(string $owner, string $repo, int $runId): array
    {
        return $this->rest()->post("/repos/{$owner}/{$repo}/actions/runs/{$runId}/cancel");
    }

    /**
     * List packages for an organization.
     *
     * @return Collection<int, PackageDTO>
     */
    public function orgPackages(string $org, string $packageType = 'container'): Collection
    {
        $type = rawurlencode($packageType);
        $data = $this->cacheRememberWithTtlOverride(
            'packages',
            "packages:org:$org:$type",
            fn () => $this->rest()->get("/orgs/{$org}/packages?package_type={$type}")
        );

        return collect($data)->map(fn (array $p) => PackageDTO::fromArray($p));
    }

    /**
     * List Dependabot alerts for a repository.
     *
     * @return Collection<int, DependabotAlertDTO>
     */
    public function dependabotAlerts(string $owner, string $repo, array $params = []): Collection
    {
        $query = $params ? ('?'.http_build_query($params)) : '';
        $data = $this->cacheRememberWithTtlOverride(
            'dependabot',
            "dependabot:$owner/$repo:".md5($query),
            fn () => $this->rest()->get("/repos/{$owner}/{$repo}/dependabot/alerts{$query}")
        );

        return collect($data)->map(fn (array $a) => DependabotAlertDTO::fromArray($a));
    }

    /**
     * List Projects V2 for a user or organization (GraphQL).
     *
     * @return Collection<int, ProjectV2DTO>
     */
    public function projectsV2(string $ownerOrOrg): Collection
    {
        $query = <<<'GQL'
query($owner:String!){
  organization(login:$owner){ projectsV2(first:20){ nodes{ id title number } } }
  user(login:$owner){ projectsV2(first:20){ nodes{ id title number } } }
}
GQL;
        $result = $this->graphql($query, ['owner' => $ownerOrOrg]);
        $nodes = [];
        if (! empty($result['data']['organization']['projectsV2']['nodes'])) {
            $nodes = array_merge($nodes, $result['data']['organization']['projectsV2']['nodes']);
        }
        if (! empty($result['data']['user']['projectsV2']['nodes'])) {
            $nodes = array_merge($nodes, $result['data']['user']['projectsV2']['nodes']);
        }

        return collect($nodes)->map(fn (array $n) => ProjectV2DTO::fromNode($n));
    }

    /**
     * Execute a GraphQL query against the GitHub API.
     *
     * In tests you can inject a fake response by setting 'graphql_fake' in config.
     *
     * @return array<string,mixed>
     */
    public function graphql(string $query, array $variables = []): array
    {
        if (isset($this->config['graphql_fake'])) {
            return $this->config['graphql_fake'];
        }

        $http = $this->client->getHttpClient();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $body = json_encode(['query' => $query, 'variables' => $variables], JSON_THROW_ON_ERROR);
        $request = $requestFactory->createRequest('POST', 'https://api.github.com/graphql')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json')
            ->withBody($streamFactory->createStream($body));

        $response = $http->sendRequest($request);
        $decoded = json_decode((string) $response->getBody(), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Verify a GitHub webhook HMAC signature (sha256).
     */
    public function verifyWebhookSignature(string $secret, string $payload, string $signatureHeader): bool
    {
        $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

        return function_exists('hash_equals') ? hash_equals($expected, $signatureHeader) : $expected === $signatureHeader;
    }

    /**
     * Allows overriding the Rest client (mainly for testing).
     */
    public function setRest(object $rest): void
    {
        $this->restOverride = $rest;
    }

    /**
     * Get the Rest client, real or fake.
     */
    private function rest(): object
    {
        if ($this->restOverride !== null) {
            return $this->restOverride;
        }

        $emit = $this->events
            ? fn (string $m, string $u, array $h, ?array $b = null) => event(new RequestSending($m, $u, $h, $b))
            : null;

        return new Rest($this->client, $emit);
    }

    // --- internals ---

    /**
     * Cache helper with default TTL.
     *
     * @param  callable():array<string,mixed>|array<int,mixed>  $callback
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRemember(string $key, callable $callback): array
    {
        $ttl = (int) ($this->config['cache']['ttl'] ?? 300);

        return $this->cacheRememberWithTtl($key, $ttl, $callback);
    }

    /**
     * Cache helper with per-domain TTL override.
     *
     * @param  callable():array<string,mixed>|array<int,mixed>  $callback
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRememberWithTtlOverride(string $domain, string $key, callable $callback): array
    {
        $defaultTtl = (int) ($this->config['cache']['ttl'] ?? 300);
        $overrides = $this->config['cache']['ttls'] ?? [];
        $ttl = (int) ($overrides[$domain] ?? $defaultTtl);

        return $this->cacheRememberWithTtl($key, $ttl, $callback);
    }

    /**
     * Low-level cache helper.
     *
     * @param  callable():array<string,mixed>|array<int,mixed>  $callback
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRememberWithTtl(string $key, int $ttl, callable $callback): array
    {
        $prefix = (string) ($this->config['cache']['prefix'] ?? 'github:');
        /** @var array $result */
        $result = $this->cache->remember($prefix.$key, $ttl, $callback);

        return $result;
    }

    /**
     * Run a callback with application-side rate limiting.
     */
    private function rateLimited(string $key, callable $callback): mixed
    {
        $limiter = new RateLimiter(
            cache: $this->cache,
            max: (int) ($this->config['rate_limiter']['max'] ?? 60),
            decaySeconds: (int) ($this->config['rate_limiter']['decay_seconds'] ?? 60),
        );

        return $limiter->attempt($key, $callback);
    }
}
