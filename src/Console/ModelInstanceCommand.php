<?php

namespace Saggre\LaravelModelInstance\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Saggre\LaravelModelInstance\Traits\CreatesInstances;
use Spatie\ModelInfo\Attributes\Attribute;
use Spatie\ModelInfo\ModelInfo;
use Spatie\ModelInfo\Relations\Relation;

class ModelInstanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instantiate {model}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new instance of an Eloquent model';

    public function __construct(
        protected ModelInstanceCommandService $modelInstanceCommandService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $classPath = $this->handleClassPath();
            $instance  = $this->createModelInstance($classPath);

            if ( ! $instance) {
                throw new Exception('Instance creation skipped');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param string $classPath
     *
     * @return Model|null
     * @throws Exception
     */
    public function createModelInstance(string $classPath): ?Model
    {
        $instance = new $classPath;
        $info     = ModelInfo::forModel($instance);

        /** @var Model&CreatesInstances $instance */

        $this->handleAttributes($instance, $info);

        // $this->handleRelations($instance, $info);

        return $this->saveModelInstance($instance);
    }

    /**
     * Finalize the creation of the model.
     *
     * @param Model $instance
     *
     * @return Model|null Saved model or null if none was saved.
     */
    public function saveModelInstance(Model &$instance): ?Model
    {
        $this->info($instance->toJson(JSON_PRETTY_PRINT));
        $modelName = class_basename($instance);

        if ($this->confirm("Create a $modelName", true)) {
            $instance->save();
        } else {
            return null;
        }

        return $instance;
    }

    /**
     * Query the user for the instantiated model's class path.
     *
     * @return string
     * @throws Exception
     */
    public function handleClassPath(): string
    {
        $inputModel = $this->argument('model');

        if (empty($inputModel)) {
            throw new Exception('No model name provided');
        }

        $classPaths = $this->modelInstanceCommandService->findAppModelCandidateClassPaths($inputModel);

        if ($classPaths->isEmpty()) {
            throw new Exception("No class found for $inputModel");
        } elseif ($classPaths->count() === 1) {
            $classPath = $classPaths->first();
        } else {
            $classPath = $this->choice('Select class path', $classPaths->toArray());
        }

        $this->output->info("Selected $classPath");

        if ( ! class_exists($classPath)) {
            throw new Exception("No class path found for $classPath");
        }

        return $classPath;
    }

    /**
     * Query the user for the instantiated model's properties.
     *
     * @param Model $instance
     * @param ModelInfo $info
     *
     * @return void
     */
    public function handleAttributes(Model &$instance, ModelInfo $info): void
    {
        $attributes = $info->attributes
            ->sortBy('name')
            ->filter(fn(Attribute $attribute
            ) => $this->getHiddenAttributes($instance)->doesntContain($attribute->name))
            ->groupBy(fn(Attribute $attribute) => str_ends_with($attribute->name, '_id') ? 'relation' : 'attribute');

        $attributes->each(fn(Collection $collection) => $collection->each(
            function (Attribute $attribute) use (&$instance) {
                $key   = $attribute->name;
                $value = null;

                while ($value === null) {
                    $value = $this->ask("Set value for $key", $attribute->default ?? 'null');

                    if ($value === 'null') {
                        $value = null;
                    }

                    if ($attribute->nullable) {
                        break;
                    }
                }

                $instance->$key = $value;
            }
        ));
    }

    /**
     * @param Model $instance
     * @param ModelInfo $info
     *
     * @return void
     * @throws Exception
     */
    public function handleRelations(Model &$instance, ModelInfo $info): void
    {
        $info->relations
            ->sortBy('name')
            ->each(function (Relation $relation) use (&$instance, $info) {
                $key       = $relation->name;
                $all       = $this->getRelationAttributes($info);
                $attribute = $this->getRelationAttributes($info)->where('name', $key)->first();

                /** @var Attribute|null $attribute */

                if ( ! $attribute) {
                    throw new Exception("Relation attribute not found for $key");
                }

                while ( ! $instance->{$key}) {
                    if ($attribute->nullable && ! $this->confirm("Fill \"$key\" relation?")) {
                        return;
                    }

                    $relationInstance = $this->createModelInstance($relation->related);

                    if ($relationInstance) {
                        call_user_func([$instance, $relation->name])->save($relationInstance);
                    }
                }
            });
    }

    /**
     * Get hidden attributes.
     *
     * @param Model $instance
     *
     * @return Collection
     */
    public function getHiddenAttributes(Model &$instance): Collection
    {
        return collect(array_merge(
            $instance->instanceHidden ?? [],
            [
                'id',
                'created_at',
                'updated_at',
            ]
        ));
    }

    /**
     * Get relation attributes (like user_id attribute for user relation etc.).
     *
     * @param ModelInfo $info
     *
     * @return Collection
     */
    public function getRelationAttributes(ModelInfo $info): Collection
    {
        return $info->attributes
            ->filter(
                fn(Attribute $attribute) => $info->relations
                    ->pluck('name')
                    ->contains(preg_replace('/_id$/', '', $attribute->name))
            );
    }
}
