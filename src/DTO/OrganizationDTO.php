<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub organization.
 */
final readonly class OrganizationDTO
{
    public function __construct(
        public string $login,
        public ?string $name = null,
        public ?string $description = null,
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
            $data['description'] ?? null,
        );
    }
}
