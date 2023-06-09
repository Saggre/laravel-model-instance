<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Console;

use Illuminate\Testing\PendingCommand;
use Saggre\LaravelModelInstance\Testbench\App\Enums\PizzaTypeEnum;
use Saggre\LaravelModelInstance\Testbench\App\Models\MayoSauce;
use Saggre\LaravelModelInstance\Testbench\App\Models\Sauce;
use Saggre\LaravelModelInstance\Tests\TestCase;
use Symfony\Component\Console\Command\Command;

class ModelInstanceCommandTest extends TestCase
{
    public function instantiateDataProvider(): array
    {
        return [
            'Create a pizza instance'                       => [
                ['instantiate Pizza', []],
                fn(PendingCommand $command, TestCase $test) => $command
                    ->expectsChoice(
                        'Set value for crust',
                        PizzaTypeEnum::PAN_PIZZA->value,
                        array_column(PizzaTypeEnum::cases(), 'value')
                    )
                    ->expectsConfirmation('Set value for has_sauce', 'yes')
                    ->expectsQuestion('Set value for name', 'Foo')
                    ->expectsQuestion('Set value for password', '12345678')
                    ->expectsConfirmation('Create a Pizza', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a sauce instance with a pizza relation' => [
                ['instantiate Sauce', []],
                fn(PendingCommand $command, TestCase $test) => $command
                    ->expectsChoice('Select class path', Sauce::class, [
                        Sauce::class,
                        MayoSauce::class,
                    ])
                    ->expectsQuestion('Set value for name', 'Bar')
                    ->expectsQuestion('Set value for spiciness', 100)
                    ->expectsConfirmation('Set value for pizza_id', 1)
                    ->expectsConfirmation('Create a Sauce', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a topping instance'                     => [
                ['instantiate Topping', []],
                fn(PendingCommand $command, TestCase $test) => $command
                    ->expectsQuestion('Set value for name', 'Baz')
                    ->expectsQuestion('Set value for price', 2000)
                    ->expectsConfirmation('Create a Topping', 'yes')
                    ->assertExitCode(Command::SUCCESS)
            ],
            'Create a non-existend instance'                => [
                ['instantiate Foobar', []],
                fn(PendingCommand $command, TestCase $test) => $command
                    ->expectsOutput('No class found for Foobar')
                    ->assertExitCode(Command::FAILURE)
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
        $expects($this->artisan(...$command), $this);
    }
}