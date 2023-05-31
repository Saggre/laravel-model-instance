<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Console;

use Illuminate\Testing\PendingCommand;
use Saggre\LaravelModelInstance\Tests\TestCase;
use Symfony\Component\Console\Command\Command;

class ModelInstanceCommandTest extends TestCase
{
    public function instantiateDataProvider(): array
    {
        return [
            'Create a pizza instance'                        => [
                ['instantiate Pizza', []],
                fn(PendingCommand $command) => $command
                    ->expectsQuestion('Set value for name', 'Foo')
                    ->expectsConfirmation('Create a Pizza', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a sauce instance with no pizza relation' => [
                ['instantiate Sauce', []],
                fn(PendingCommand $command) => $command
                    ->expectsQuestion('Set value for name', 'Bar')
                    ->expectsQuestion('Set value for spiciness', 100)
                    ->expectsConfirmation('Set value for pizza_id', 'null')
                    ->expectsConfirmation('Create a Sauce', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a sauce instance with a pizza relation'  => [
                ['instantiate Sauce', []],
                fn(PendingCommand $command) => $command
                    ->expectsQuestion('Set value for name', 'Bar')
                    ->expectsQuestion('Set value for spiciness', 100)
                    ->expectsConfirmation('Set value for pizza_id', 1)
                    ->expectsConfirmation('Create a Sauce', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a topping instance'                      => [
                ['instantiate Topping', []],
                fn(PendingCommand $command) => $command
                    ->expectsQuestion('Set value for name', 'Baz')
                    ->expectsQuestion('Set value for price', 2000)
                    ->expectsConfirmation('Set value for pizza_id', 'null')
                    ->expectsConfirmation('Create a Topping', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
        ];
    }

    /**
     * @dataProvider instantiateDataProvider
     *
     * @param array $command
     * @param callable $expects
     *
     * @return void
     */
    public function test_instantiate(array $command, callable $expects): void
    {
        $expects($this->artisan(...$command));
    }
}