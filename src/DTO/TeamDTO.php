<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing an organization team.
 */
final readonly class TeamDTO
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {}

    /**
     * Create the DTO from a GitHub API array payload.
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['name'],
            (string) $data['slug'],
        );
    }
}
