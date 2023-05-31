<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Services;

use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Saggre\LaravelModelInstance\Tests\TestCase;
use Saggre\LaravelModelInstance\Tests\TestModelInstanceCommandService;

class ModelInstanceCommandServiceTest extends TestCase
{
    protected ModelInstanceCommandService $modelInstanceCommandService;

    public function setUp(): void
    {
        parent::setUp();
        $this->modelInstanceCommandService = new TestModelInstanceCommandService();
    }

    public function modelNames(): array
    {
        return [
            ['UserRole', 'App\Models\UserRole'],
            ['UserRole', 'Foo/Bar/UserRole'],
            ['UserRole', 'userRole'],
            ['UserRole', 'UserRole'],
            ['UserRole', 'User-Role'],
            ['UserRole', 'user-role'],
            ['UserRole', 'user_role'],
        ];
    }

    /**
     * @dataProvider modelNames
     */
    public function test_normalize_model_name(string $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->normalizeModelName($input)
        );
    }

    public function candidateTestCases(): array
    {
        return [
            [
                [
                    'Saggre\\LaravelModelInstance\\Testbench\\App\\Models\\Pizza',
                ],
                'Pizza',
            ],
            [
                [
                    'Saggre\\LaravelModelInstance\\Testbench\\App\\Models\\Topping',
                ],
                'Topping',
            ],
            [
                [
                    'Saggre\\LaravelModelInstance\\Testbench\\App\\Models\\Sauce',
                    'Saggre\\LaravelModelInstance\\Testbench\\App\\Models\\MayoSauce',
                ],
                'Sauce',
            ]
        ];
    }

    /**
     * @dataProvider candidateTestCases
     */
    public function test_find_app_model_candidate_class_paths(array $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->findAppModelCandidateClassPaths($input)->toArray()
        );
    }
}
