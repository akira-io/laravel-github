<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub repository.
 */
final readonly class RepoDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $full_name,
        public bool $private,
        public ?string $description = null,
        public ?string $language = null,
        public ?int $stargazers_count = null,
        public ?int $forks_count = null,
    ) {}

    /**
     * Create the DTO from a GitHub API array payload.
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            (string) $data['name'],
            (string) $data['full_name'],
            (bool) $data['private'],
            $data['description'] ?? null,
            $data['language'] ?? null,
            isset($data['stargazers_count']) ? (int) $data['stargazers_count'] : null,
            isset($data['forks_count']) ? (int) $data['forks_count'] : null,
        );
    }
}
