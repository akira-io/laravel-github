<?php

declare(strict_types=1);

namespace Akira\GitHub\Tests\Support;

use Github\Client;

/**
 * Super-slim stub for Github\Client and its sub-APIs used in tests.
 *
 * You pass an array of fixtures in the constructor, and the stub
 * will return them instead of calling the real GitHub API.
 *
 * Example:
 * $client = new StubGithubClient([
 *     'repo.show' => [
 *         'akira/hunter' => ['id' => 99, 'name' => 'hunter', 'full_name' => 'akira/hunter', 'private' => false],
 *     ],
 * ]);
 * $repo = $client->repo()->show('akira', 'hunter');
 * // → returns the fixture with id=99
 */
final class StubGithubClient extends Client
{
    public function __construct(private array $fixtures = []) {}

    public function user(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function show(string $username): array
            {
                return $this->fx['user.show'][$username] ?? ['login' => $username];
            }

            public function repositories(
                string $u,
                string $type,
                string $sort,
                string $direction,
                int $page,
                int $perPage
            ): array {
                return $this->fx['user.repositories'][$u][$page][$perPage] ?? [];
            }
        };
    }

    public function repo(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function show(string $owner, string $repo): array
            {
                $key = "$owner/$repo";

                return $this->fx['repo.show'][$key] ?? [
                    'id' => 1,
                    'name' => $repo,
                    'full_name' => $key,
                    'private' => false,
                ];
            }

            public function releases(): object
            {
                return new class($this->fx)
                {
                    public function __construct(private array $fx) {}

                    public function all(string $owner, string $repo): array
                    {
                        return $this->fx['repo.releases']["$owner/$repo"] ?? [];
                    }
                };
            }
        };
    }

    public function issue(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function all(string $owner, string $repo, array $params = []): array
            {
                return $this->fx['issue.all']["$owner/$repo"] ?? [];
            }

            public function create(string $owner, string $repo, array $payload): array
            {
                return $payload + ['number' => 1, 'state' => 'open'];
            }

            public function comments(): object
            {
                return new class($this->fx)
                {
                    public function __construct(private array $fx) {}

                    public function create(string $owner, string $repo, int $num, array $body): array
                    {
                        return ['ok' => true] + $body;
                    }
                };
            }
        };
    }

    public function pullRequest(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function all(string $owner, string $repo, array $params = []): array
            {
                return $this->fx['pr.all']["$owner/$repo"] ?? [];
            }
        };
    }

    public function organization(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function show(string $org): array
            {
                return $this->fx['org.show'][$org] ?? ['login' => $org];
            }

            public function repositories(string $org, array $params = []): array
            {
                return $this->fx['org.repositories'][$org] ?? [];
            }
        };
    }

    public function team(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function all(string $org): array
            {
                return $this->fx['team.all'][$org] ?? [];
            }
        };
    }

    public function gist(): object
    {
        return new class($this->fixtures)
        {
            public function __construct(private array $fx) {}

            public function all(string $username, array $params = []): array
            {
                return $this->fx['gist.all'][$username] ?? [];
            }

            public function show(string $id): array
            {
                return $this->fx['gist.show'][$id] ?? ['id' => $id];
            }
        };
    }
}
