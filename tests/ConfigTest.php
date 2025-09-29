<?php

declare(strict_types=1);

it('has default config values', function () {
    $config = require __DIR__.'/../config/github.php';
    expect($config['auth'])->toBe('token')
        ->and($config['pagination']['per_page'])->toBe(30);
});
