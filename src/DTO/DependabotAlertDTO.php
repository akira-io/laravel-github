<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a Dependabot alert.
 */
final readonly class DependabotAlertDTO
{
    public function __construct(
        public int $number,
        public string $state,
        public ?string $summary = null,
    ) {}

    /**
     * Create the DTO from a GitHub API array payload.
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $summary = $data['security_advisory']['summary'] ?? null;

        return new self(
            (int) ($data['number'] ?? 0),
            (string) ($data['state'] ?? 'open'),
            $summary
        );
    }
}
