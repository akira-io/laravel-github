<?php

declare(strict_types=1);

namespace Akira\GitHub\Events;

/**
 * Event fired when the package-side rate limiter is exceeded.
 */
final readonly class RateLimited
{
    public function __construct(
        public string $key,
        public int $max,
        public int $decaySeconds,
    ) {}
}
