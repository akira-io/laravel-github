<?php

declare(strict_types=1);

namespace Akira\GitHub\DTO;

/**
 * Data Transfer Object representing a Projects V2 node.
 */
final readonly class ProjectV2DTO
{
    public function __construct(
        public string $id,
        public string $title,
        public int $number,
    ) {}

    /**
     * Create the DTO from a GraphQL node payload.
     *
     * @param  array<string,mixed>  $n
     */
    public static function fromNode(array $n): self
    {
        return new self(
            (string) $n['id'],
            (string) $n['title'],
            (int) $n['number'],
        );
    }
}
