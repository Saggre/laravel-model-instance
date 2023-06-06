<?php

namespace Saggre\LaravelModelInstance\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Saggre\LaravelModelInstance\Traits\Instantiable;
use Spatie\ModelInfo\Attributes\Attribute;
use Spatie\ModelInfo\ModelInfo;

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

            $this->info($instance->toJson(JSON_PRETTY_PRINT));
            $modelName = class_basename($instance);
            $this->info("Created a $modelName");
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Create a model instance.
     *
     * @param string $classPath
     *
     * @return Model|null
     * @throws Exception
     */
    public function createModelInstance(string $classPath): ?Model
    {
        $instance = new $classPath;
        $info     = ModelInfo::forModel($instance);

        /** @var Model $instance */

        $this->handleAttributes($instance, $info);

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
            ) => $this->getHiddenAttributes($instance, $info)->doesntContain($attribute->name))
            ->groupBy(fn(Attribute $attribute) => str_ends_with($attribute->name, '_id') ? 'relation' : 'attribute');

        $attributes->each(fn(Collection $collection) => $collection->each(
            function (Attribute $attribute) use (&$instance) {
                $key   = $attribute->name;
                $value = null;

                while ($value === null) {
                    $default = $this->getAttributeDefaultValue($attribute, $instance);

                    try {
                        $options = $this->getAttributeOptions($attribute, $instance);
                        $value   = $this->choice("Set value for $key", $options, $default);
                    } catch (Exception $e) {
                        $value = $this->ask("Set value for $key", $default);
                    }

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
     * Get options for an attribute.
     *
     * @param Attribute $attribute
     *
     * @return array
     * @throws Exception
     */
    public function getAttributeOptions(Attribute $attribute): array
    {
        if ($attribute->cast && enum_exists($attribute->cast)) {
            $cases = call_user_func([$attribute->cast, 'cases']);

            return array_column($cases, 'value');
        }

        throw new Exception('No options found for attribute');
    }

    /**
     * Get default value for an attribute.
     *
     * @param Attribute $attribute
     * @param Model&Instantiable $instance
     *
     * @return mixed|null
     */
    public function getAttributeDefaultValue(Attribute $attribute, Model $instance): mixed
    {
        if (method_exists(
                $instance,
                'getInstantiationDefaults'
            ) && $instance->getInstantiationDefaults()->has($attribute->name)) {
            return $instance->getInstantiationDefaults()->get($attribute->name);
        }

        return $attribute->default ?? null;
    }

    /**
     * Get attributes that don't need to be prompted to the user.
     *
     * @param Model&Instantiable $instance
     * @param ModelInfo $info
     *
     * @return Collection
     */
    public function getHiddenAttributes(Model &$instance, ModelInfo $info): Collection
    {
        $attributes = $info->attributes
            ->filter(fn(Attribute $attribute) => $attribute->nullable || $attribute->default !== null)
            ->map(fn(Attribute $attribute) => $attribute->name);

        if (method_exists($instance, 'getInstantiableProperties')) {
            $instantiableProperties = $instance->getInstantiableProperties();
        } else {
            $instantiableProperties = collect();
        }

        return collect([
            'id',
            'created_at',
            'updated_at',
        ])->merge($attributes)
          ->merge($instantiableProperties);
    }
}
