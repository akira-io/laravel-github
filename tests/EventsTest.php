<?php

declare(strict_types=1);

use Akira\GitHub\Events\RateLimited;
use Akira\GitHub\Events\RequestSending;
use Akira\GitHub\Events\ResponseReceived;

it('instantiates events', function () {
    $a = new RequestSending('GET', 'u', ['A' => 'B'], null);
    $b = new ResponseReceived(200, 'u', '{}');
    $c = new RateLimited('key', 60, 60);

    expect($a->method)->toBe('GET')
        ->and($b->status)->toBe(200)
        ->and($c->max)->toBe(60);
});
