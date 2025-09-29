<?php

declare(strict_types=1);

use Akira\GitHub\DTO\PackageDTO;

it('PackageDTO from array', fn () => expect(PackageDTO::fromArray(['name' => 'image', 'package_type' => 'container']))->package_type->toBe('container'));
