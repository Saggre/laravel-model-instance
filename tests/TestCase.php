<?php

namespace Saggre\LaravelModelInstance\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        return [TestModelInstanceServiceProvider::class];
    }

    public static function applicationBasePath(): string
    {
        return dirname(__DIR__).'/workbench';
    }
}
