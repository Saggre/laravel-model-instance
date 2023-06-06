<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Saggre\LaravelModelInstance\Testbench\App\Models\Pizza;
use Saggre\LaravelModelInstance\Tests\TestCase;
use Saggre\LaravelModelInstance\Tests\TestModelInstanceCommandService;
use Spatie\ModelInfo\Attributes\Attribute;
use Spatie\ModelInfo\ModelInfo;

class ModelInstanceCommandServiceTest extends TestCase
{
    protected ModelInstanceCommandService $modelInstanceCommandService;

    public function setUp(): void
    {
        parent::setUp();
        $this->modelInstanceCommandService = new TestModelInstanceCommandService();
    }

    public function normalizeModelNameTestCases(): array
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
     * @dataProvider normalizeModelNameTestCases
     */
    public function test_normalize_model_name(string $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->normalizeModelName($input)
        );
    }

    public function findAppModelCandidateClassPathsTestCases(): array
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
     * @dataProvider findAppModelCandidateClassPathsTestCases
     */
    public function test_find_app_model_candidate_class_paths(array $expected, string $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->findAppModelCandidateClassPaths($input)->toArray()
        );
    }

    /**
     * @throws Exception
     */
    /*public function test_get_attribute_options(array $expected, Attribute $input)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->getAttributeOptions($input)
        );
    }

    public function test_filter_attribute_value(mixed $expected, Attribute $input, Model $model, mixed $value)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->filterAttributeValue($input, $model, $value)
        );
    }

    public function getAttributeDefaultValueTestCases(): array
    {
        $pizza = Pizza::factory()->create([
            'name' => 'Margherita',
        ]);

        $info = ModelInfo::forModel($pizza);

        return [
            [
                'Margherita',
                $info->attributes->firstWhere('name', 'name'),
                $pizza,
            ],
        ];
    }

    public function test_get_attribute_default_value(mixed $expected, Attribute $input, Model $model)
    {
        $this->assertEquals(
            $expected,
            $this->modelInstanceCommandService->getAttributeDefaultValue($input, $model)
        );
    }*/
}
