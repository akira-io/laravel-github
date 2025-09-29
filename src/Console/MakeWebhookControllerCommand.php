<?php

declare(strict_types=1);

namespace Akira\GitHub\Console;

use Illuminate\Console\GeneratorCommand;

/**
 * Artisan generator for a GitHub webhook controller.
 */
final class MakeWebhookControllerCommand extends GeneratorCommand
{
    protected $name = 'github:webhook-controller';

    protected $description = 'Create a GitHub webhook controller with signature validation';

    protected $type = 'Controller';

    protected function getStub(): string
    {
        return __DIR__.'/stubs/webhook-controller.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Http\\Controllers';
    }
}
