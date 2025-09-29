<?php

declare(strict_types=1);

namespace Akira\GitHub\Tests;

use Akira\GitHub\GitHubServiceProvider;
use Orchestra\Testbench\TestCase as Base;

/**
 * Base TestCase bootstrapping the package provider.
 */
abstract class TestCase extends Base
{
    protected function getPackageProviders($app): array
    {
        return [GitHubServiceProvider::class];
    }
}
