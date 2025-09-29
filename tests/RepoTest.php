<?php

declare(strict_types=1);

use Akira\GitHub\DTO\RepoDTO;

it('builds a RepoDTO from array', function () {
    $dto = RepoDTO::fromArray(['id' => 1, 'name' => 'demo', 'full_name' => 'akira/demo', 'private' => false]);
    expect($dto->full_name)->toBe('akira/demo');
});
