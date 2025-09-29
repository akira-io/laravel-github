<?php

declare(strict_types=1);

use Akira\GitHub\DTO\TeamDTO;

it('TeamDTO from array', fn () => expect(TeamDTO::fromArray(['name' => 'Core', 'slug' => 'core']))->slug->toBe('core'));
