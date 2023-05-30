<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Console;

use Saggre\LaravelModelInstance\Tests\TestCase;

class ModelInstanceCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_instantiate_pizza(): void
    {
        $this->artisan('instantiate Pizza')->assertExitCode(0);
    }
}