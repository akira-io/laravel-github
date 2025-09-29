<?php

declare(strict_types=1);

use Akira\GitHub\Services\GitHubManager;
use Github\Client;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

it('verifies webhook signature', function () {
    $client = new Client();
    $cache = new Repository(new ArrayStore());
    $manager = new GitHubManager($client, $cache, ['cache' => ['ttl' => 60, 'prefix' => 't:']]);

    $payload = '{"action":"opened"}';
    $secret = 'secret';
    $sig = 'sha256='.hash_hmac('sha256', $payload, $secret);

    expect($manager->verifyWebhookSignature($secret, $payload, $sig))->toBeTrue();
});
