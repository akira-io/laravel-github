<?php

declare(strict_types=1);

use Akira\GitHub\DTO\ReleaseDTO;

it('ReleaseDTO from array', fn () => expect(ReleaseDTO::fromArray(['id' => 3, 'tag_name' => 'v1.0.0']))->tag_name->toBe('v1.0.0'));
