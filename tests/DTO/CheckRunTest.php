<?php

declare(strict_types=1);

use Akira\GitHub\DTO\CheckRunDTO;

it('CheckRunDTO from array', fn () => expect(CheckRunDTO::fromArray(['id' => 2, 'name' => 'lint', 'status' => 'completed']))->name->toBe('lint'));
