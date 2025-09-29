<?php

declare(strict_types=1);

use Akira\GitHub\DTO\PullRequestDTO;

it('PullRequestDTO from array', fn () => expect(PullRequestDTO::fromArray(['number' => 7, 'title' => 'Add', 'state' => 'open']))->title->toBe('Add'));
