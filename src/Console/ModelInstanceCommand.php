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
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $instance  = new $classPath;
        $modelInfo = ModelInfo::forModel($classPath);
        $modelName = class_basename($classPath);

        /** @var Model&CreatesInstances $instance */

        $this->handleAttributes($modelInfo, $instance);

        $this->info($instance->toJson(JSON_PRETTY_PRINT));

        if ($this->confirm("Create a $modelName", true)) {
            $instance->save();
        } else {
            $this->output->error('Aborted instance creation');

            return self::FAILURE;
        }

        return self::SUCCESS;
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
     * Get hidden attributes.
     *
     * @return Collection
     */
    public function getHiddenAttributes(): Collection
    {
        return collect(array_merge(
            $modelInstance->instanceHidden ?? [],
            [
                'id',
                'created_at',
                'updated_at',
            ]
        ));
    }

    /**
     * Query the user for the instantiated model's properties.
     *
     * @param ModelInfo $modelInfo
     * @param $instance
     *
     * @return void
     */
    public function handleAttributes(ModelInfo $modelInfo, &$instance): void
    {
        $modelInfo->attributes
            ->sortBy('name')
            ->filter(fn(Attribute $attribute) => ! $this->getHiddenAttributes()->contains($attribute->name))
            ->each(function (Attribute $attribute) use (&$instance) {
                $key   = $attribute->name;
                $value = null;

                while ($value === null) {
                    $value = $this->ask("Set value for $key", $attribute->default);
                    /*if ($value === null && ! $attribute->nullable) {
                        $this->output->info("Attribute $key is not nullable. Set a new value");
                        continue;
                    }

                    break;*/
                }

                $instance->$key = $value;
            });
    }
}
