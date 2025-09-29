<?php

declare(strict_types=1);

use Akira\GitHub\DTO\GistDTO;

it('GistDTO from array', fn () => expect(GistDTO::fromArray(['id' => '1']))->id->toBe('1'));
