<?php

namespace Saggre\LaravelModelInstance\Tests\Unit\Services;

use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Saggre\LaravelModelInstance\Tests\TestCase;

class ModelInstanceCommandServiceTest extends TestCase
{
    protected ModelInstanceCommandService $modelInstanceCommandService;

    public function setUp(): void
    {
        parent::setUp();
        $this->modelInstanceCommandService = new class extends ModelInstanceCommandService {
            /**
             * Get mock class paths.
             *
             * @return Collection
             */
            public function getAppModelClassPaths(): Collection
            {
                return collect([
                    'Foo\\Baz\\Bar',
                    'Foo\\Bar',
                    'Foo\\Bar\\Baz',
                    'Foo\\Baz',
                    'Baz\\Bar\\Foo',
                    'Bar\\Foo',
                ]);
            }
        };
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
                    'Baz\\Bar\\Foo',
                    'Bar\\Foo',
                ],
                'Foo',
            ],
            [
                [
                    'Foo\\Bar\\Baz',
                    'Foo\\Baz',
                ],
                'Baz',
            ],
            [
                [
                    'Baz\\Bar\\Foo',
                    'Bar\\Foo',
                ],
                'Foo',
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
