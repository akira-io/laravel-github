<?php

declare(strict_types=1);

use Akira\GitHub\DTO\ActionsRunDTO;

it('ActionsRunDTO from array', fn () => expect(ActionsRunDTO::fromArray(['id' => 1, 'name' => 'ci', 'status' => 'completed']))->status->toBe('completed'));
