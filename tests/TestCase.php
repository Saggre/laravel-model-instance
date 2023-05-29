<?php

namespace Saggre\LaravelModelInstance\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Saggre\LaravelModelInstance\ModelInstanceServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
        });
    }

    protected function getPackageProviders($app): array
    {
        return [ModelInstanceServiceProvider::class];
    }

    public static function applicationBasePath(): string
    {
        return __DIR__.'/../skeleton';
    }
}
