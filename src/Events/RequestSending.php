<?php

declare(strict_types=1);

namespace Akira\GitHub\Events;

/**
 * Event fired before sending an HTTP request to GitHub.
 */
final readonly class RequestSending
{
    public function __construct(
        public string $method,
        public string $url,
        /** @var array<string,string> */
        public array $headers = [],
        public ?string $body = null,
    ) {}
}
