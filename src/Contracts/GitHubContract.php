<?php

declare(strict_types=1);

namespace Akira\GitHub\Contracts;

use Akira\GitHub\DTO\RepoDTO;
use Akira\GitHub\DTO\UserDTO;
use Illuminate\Support\Collection;

/**
 * Contract for GitHub API interactions, returning typed DTOs and Collections.
 */
interface GitHubContract
{
    /**
     * Fetch a GitHub user by username.
     */
    public function user(string $username): UserDTO;

    /**
     * List repositories for the given user.
     *
     * @return Collection<int, RepoDTO>
     */
    public function userRepos(string $username, int $page = 1, ?int $perPage = null): Collection;
}
