<?php

declare(strict_types=1);

use Akira\GitHub\DTO\OrganizationDTO;

it('OrganizationDTO from array', fn () => expect(OrganizationDTO::fromArray(['login' => 'akira-io', 'name' => 'Akira']))->login->toBe('akira-io'));
