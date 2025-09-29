<?php

declare(strict_types=1);

namespace Akira\GitHub\Tests\Support;

/**
 * Fake Rest helper returning canned arrays for tests.
 */
final class FakeRest
{
    public function __construct(private array $responses = []) {}

    public function when(string $key, array $value): self
    {
        $this->responses[$key] = $value;

        return $this;
    }

    public function get(string $path, array $headers = []): array
    {
        return $this->responses['GET '.$path] ?? [];
    }

    public function post(string $path, array $body = [], array $headers = []): array
    {
        return $this->responses['POST '.$path] ?? ['ok' => true];
    }

    public function download(string $path, string $dest): string
    {
        file_put_contents($dest, 'zip-bytes');

        return $dest;
    }
}
