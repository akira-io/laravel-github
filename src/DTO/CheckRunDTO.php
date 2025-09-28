<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a Check Run for a commit.
 */
final readonly class CheckRunDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $status,
        public ?string $conclusion = null,
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
            (string) $data['status'],
            $data['conclusion'] ?? null,
        );
    }
}
