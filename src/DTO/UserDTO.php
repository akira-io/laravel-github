<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub user.
 */
final readonly class UserDTO
{
    public function __construct(
        public string $login,
        public ?string $name = null,
        public ?string $company = null,
        public ?string $location = null,
        public ?string $email = null,
        public ?string $avatar_url = null,
        public ?string $bio = null,
        public ?int $public_repos = null,
        public ?int $followers = null,
        public ?int $following = null,
    ) {}

    /**
     * Create the DTO from a GitHub API array payload.
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['login'],
            $data['name'] ?? null,
            $data['company'] ?? null,
            $data['location'] ?? null,
            $data['email'] ?? null,
            $data['avatar_url'] ?? null,
            $data['bio'] ?? null,
            isset($data['public_repos']) ? (int) $data['public_repos'] : null,
            isset($data['followers']) ? (int) $data['followers'] : null,
            isset($data['following']) ? (int) $data['following'] : null,
        );
    }
}
