<?php

declare(strict_types=1);

namespace Akira\GitHub\Services;

use Akira\GitHub\Contracts\GitHubContract;
use Akira\GitHub\DTO\RepoDTO;
use Akira\GitHub\DTO\UserDTO;
use Illuminate\Support\Collection;

/**
 * Fake implementation of GitHubContract for testing purposes.
 *
 * Allows registering deterministic DTOs for specific method keys.
 */
final class GitHubFake implements GitHubContract
{
    /** @var array<string, mixed> */
    private array $responses = [];

    /**
     * Register a fake return value for a specific key.
     *
     * @param  string  $key  e.g.: "user:octocat" or "userRepos:octocat:1:30"
     * @param  mixed  $value  DTO instance or array payload
     */
    public function set(string $key, mixed $value): void
    {
        $this->responses[$key] = $value;
    }

    public function user(string $username): UserDTO
    {
        $value = $this->responses["user:$username"] ?? new UserDTO($username);

        return $value instanceof UserDTO ? $value : UserDTO::fromArray((array) $value);
    }

    /**
     * @return Collection<int, RepoDTO>
     */
    public function userRepos(string $username, int $page = 1, ?int $perPage = null): Collection
    {
        $value = $this->responses["userRepos:$username:$page:$perPage"] ?? [];
        $arr = is_array($value) ? $value : [];

        return collect($arr)->map(fn ($v) => $v instanceof RepoDTO ? $v : RepoDTO::fromArray((array) $v));
    }
}
