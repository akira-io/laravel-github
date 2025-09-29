<?php

declare(strict_types=1);

use Akira\GitHub\DTO\ProjectV2DTO;

it('ProjectV2DTO from node', fn () => expect(ProjectV2DTO::fromNode(['id' => 'P', 'title' => 'Roadmap', 'number' => 1]))->title->toBe('Roadmap'));
