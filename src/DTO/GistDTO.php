<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub gist.
 */
final readonly class GistDTO
{
    public function __construct(
        public string $id,
        public ?string $description = null,
        public ?string $html_url = null,
    ) {}

    /**
     * Create the DTO from a GitHub API array payload.
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['id'],
            $data['description'] ?? null,
            $data['html_url'] ?? null,
        );
    }
}
