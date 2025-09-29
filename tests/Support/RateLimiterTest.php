<?php

declare(strict_types=1);

use Akira\GitHub\Support\RateLimiter;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

it('allows within limit', function () {
    $limiter = new RateLimiter(new Repository(new ArrayStore()), 2, 60);
    $a = $limiter->attempt('key', fn () => 1);
    $b = $limiter->attempt('key', fn () => 2);
    expect($a)->toBe(1)->and($b)->toBe(2);
});

it('blocks when limit exceeded', function () {
    $limiter = new RateLimiter(new Repository(new ArrayStore()), 1, 60);
    $limiter->attempt('key', fn () => true);

    $limiter->attempt('key', fn () => true);
})->throws(RuntimeException::class);
