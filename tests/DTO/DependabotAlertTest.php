<?php

declare(strict_types=1);

use Akira\GitHub\DTO\DependabotAlertDTO;

it('DependabotAlertDTO from array', fn () => expect(DependabotAlertDTO::fromArray(['number' => 9, 'state' => 'open', 'security_advisory' => ['summary' => 'X']]))->summary->toBe('X'));
