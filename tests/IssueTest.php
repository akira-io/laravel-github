<?php

declare(strict_types=1);

use Akira\GitHub\DTO\IssueDTO;

it('builds an IssueDTO from array', function () {
    $dto = IssueDTO::fromArray(['number' => 10, 'title' => 'Bug', 'state' => 'open', 'html_url' => 'u']);
    expect($dto->number)->toBe(10)->and($dto->state)->toBe('open');
});
