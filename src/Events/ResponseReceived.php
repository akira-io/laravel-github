<?php

declare(strict_types=1);

namespace Akira\GitHub\Events;

/**
 * Event fired after receiving an HTTP response from GitHub.
 */
final readonly class ResponseReceived
{
    public function __construct(
        public int $status,
        public string $url,
        public ?string $body = null,
    ) {}
}
