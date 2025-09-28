<?php

declare(strict_types=1);

use Akira\GitHub\DTO\UserDTO;

it('builds a UserDTO from array', function () {
    $dto = UserDTO::fromArray(['login' => 'octocat', 'name' => 'The Octocat', 'followers' => 42]);
    expect($dto->login)->toBe('octocat')
        ->and($dto->name)->toBe('The Octocat')
        ->and($dto->followers)->toBe(42);
});
