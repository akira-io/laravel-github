<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub Actions workflow run.
 */
final readonly class ActionsRunDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $status,
        public ?string $conclusion = null,
        public ?string $created_at = null,
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
            (string) ($data['name'] ?? 'workflow'),
            (string) ($data['status'] ?? 'unknown'),
            $data['conclusion'] ?? null,
            $data['created_at'] ?? null,
        );
    }
}
