<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Services;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function normalizeModelNameTestCases(): array
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

    #[DataProvider('normalizeModelNameTestCases')]
    public function test_normalize_model_name(string $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->normalizeModelName($input)
        );
    }

    public static function findAppModelCandidateClassPathsTestCases(): array
    {
        return [
            [
                [
                    'App\\Models\\Pizza',
                ],
                'Pizza',
            ],
            [
                [
                    'App\\Models\\Topping',
                ],
                'Topping',
            ],
            [
                [
                    'App\\Models\\Sauce',
                    'App\\Models\\MayoSauce',
                ],
                'Sauce',
            ]
        ];
    }

    #[DataProvider('findAppModelCandidateClassPathsTestCases')]
    public function test_find_app_model_candidate_class_paths(array $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->findAppModelCandidateClassPaths($input)->toArray()
        );
    }
}
