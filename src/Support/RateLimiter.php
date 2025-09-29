<?php

declare(strict_types=1);

namespace Akira\GitHub\Support;

use Akira\GitHub\Events\RateLimited;
use Illuminate\Contracts\Cache\Repository as Cache;
use RuntimeException;
use Throwable;

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
     */
    public function attempt(string $key, callable $callback): mixed
    {
        $bucket = $this->keyPrefix.$key;
        $count = (int) $this->cache->get($bucket, 0);

        if ($count >= $this->max) {
            // Fire event only if dispatcher is available
            try {
                if (function_exists('event')) {
                    event(new RateLimited($key, $this->max, $this->decaySeconds));
                }
            } catch (Throwable) {
                // ignore if no container is booted
            }

            throw new RuntimeException('GitHub rate limit reached for '.$key);
        }

        $this->cache->put($bucket, $count + 1, $this->decaySeconds);

        return $callback();
    }
}
