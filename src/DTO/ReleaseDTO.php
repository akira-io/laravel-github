<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub release.
 */
final readonly class ReleaseDTO
{
    public function __construct(
        public int $id,
        public string $tag_name,
        public ?string $name = null,
        public ?string $url = null,
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
            (string) $data['tag_name'],
            $data['name'] ?? null,
            $data['html_url'] ?? null,
        );
    }
}
