<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Console;

use Saggre\LaravelModelInstance\Tests\TestCase;

class ModelInstanceCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_console_command(): void
    {
        $this->artisan('instantiate Foo')->assertExitCode(0);
    }
}