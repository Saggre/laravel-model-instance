<?php

namespace Saggre\LaravelModelInstance\Tests;

use Saggre\LaravelModelInstance\ModelInstanceServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ModelInstanceServiceProvider::class];
    }

    public static function applicationBasePath(): string
    {
        return __DIR__.'/../skeleton';
    }
}
