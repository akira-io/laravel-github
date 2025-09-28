<?php

declare(strict_types=1);

namespace Akira\GitHub\Services;

use Akira\GitHub\DTO\UserDTO;
use Github\Client;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class GitHubManager
{
    public function __construct(
        private Client $client,
        private CacheRepository $cache,
        /** @var array<string,mixed> */
        private array $config
    ) {}

    /**
     * Get a GitHub user by username.
     *
     * @param  string  $username  GitHub username
     */
    public function user(string $username): UserDTO
    {
        $data = $this->cacheRemember("users:$username", fn () => $this->client->user()->show($username));

        return UserDTO::fromArray($data);
    }

    /**
     * Cache helper with default TTL.
     *
     *
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRemember(string $key, callable $callback): array
    {
        $ttl = (int) ($this->config['cache']['ttl'] ?? 300);

        return $this->cacheRememberWithTtl($key, $ttl, $callback);
    }

    /**
     * Cache helper with per-domain TTL override.
     *
     *
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRememberWithTtlOverride(string $domain, string $key, callable $callback): array
    {
        $defaultTtl = (int) ($this->config['cache']['ttl'] ?? 300);
        $overrides = $this->config['cache']['ttls'] ?? [];
        $ttl = (int) ($overrides[$domain] ?? $defaultTtl);

        return $this->cacheRememberWithTtl($key, $ttl, $callback);
    }

    /**
     * Low-level cache helper.
     *
     *
     * @return array<string,mixed>|array<int,mixed>
     */
    private function cacheRememberWithTtl(string $key, int $ttl, callable $callback): array
    {
        $prefix = (string) ($this->config['cache']['prefix'] ?? 'github:');
        /** @var array $result */
        $result = $this->cache->remember($prefix.$key, $ttl, $callback);

        return $result;
    }
}
