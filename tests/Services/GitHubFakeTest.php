<?php

declare(strict_types=1);

use Akira\GitHub\DTO\UserDTO;
use Akira\GitHub\Services\GitHubFake;

it('returns faked user and repos', function () {
    $fake = new GitHubFake();
    $fake->set('user:octo', new UserDTO('octo', 'Octo'));
    $fake->set('userRepos:octo:1:30', [['id' => 1, 'name' => 'r', 'full_name' => 'octo/r', 'private' => false]]);

    $u = $fake->user('octo');
    $repos = $fake->userRepos('octo', 1, 30);

    expect($u->login)->toBe('octo')->and($repos)->toHaveCount(1);
});
