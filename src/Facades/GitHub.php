<?php

declare(strict_types=1);

namespace Akira\GitHub\Facades;

use Akira\GitHub\Services\GitHubManager;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the GitHubManager service.
 *
 * @see GitHubManager
 */
final class GitHub extends Facade
{
    /**
     * Get the IoC container binding key for the underlying service.
     */
    protected static function getFacadeAccessor(): string
    {
        return GitHubManager::class;
    }
}
