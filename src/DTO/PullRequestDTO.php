<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a GitHub pull request.
 */
final readonly class PullRequestDTO
{
    public function __construct(
        public int $number,
        public string $title,
        public string $state,
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
            (int) $data['number'],
            (string) $data['title'],
            (string) $data['state'],
            $data['html_url'] ?? null,
        );
    }
}
