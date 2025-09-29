<?php

declare(strict_types=1);

namespace Akira\GitHub\Support;

use Akira\GitHub\Events\RateLimited;
use Illuminate\Contracts\Cache\Repository as Cache;
use RuntimeException;

/**
 * Simple cache-backed rate limiter for application-side throttling.
 */
final readonly class RateLimiter
{
    public function __construct(
        private Cache $cache,
        private int $max,
        private int $decaySeconds,
        private string $keyPrefix = 'github:rate:'
    ) {}

    /**
     * Attempt to run the callback within the current budget.
     *
     * @param  string  $key  Logical rate limit key
     * @param  callable  $callback  Callback to execute
     *
     * @throws RuntimeException when the limit is exceeded
     */
    public function attempt(string $key, callable $callback): mixed
    {
        $bucket = $this->keyPrefix.$key;
        $count = (int) $this->cache->get($bucket, 0);

        if ($count >= $this->max) {
            event(new RateLimited($key, $this->max, $this->decaySeconds));
            throw new RuntimeException('GitHub rate limit reached for '.$key);
        }

        $this->cache->put($bucket, $count + 1, $this->decaySeconds);

        return $callback();
    }
}
