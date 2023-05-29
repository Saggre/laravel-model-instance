<?php

namespace Saggre\LaravelModelInstance\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
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
        $inputModel = $this->argument('model');

        if (empty($inputModel)) {
            $this->output->error('No model name provided');

            return self::FAILURE;
        }

        $classPaths = $this->modelInstanceCommandService->findAppModelCandidateClassPaths($inputModel);

        if ($classPaths->isEmpty()) {
            $this->output->error("No class found for $inputModel");

            return self::FAILURE;
        } elseif ($classPaths->count() === 1) {
            $classPath = $classPaths->first();
        } else {
            $classPath = $this->choice('Select class path', $classPaths->toArray());
        }

        $this->output->info("Selected $classPath");

        if ( ! class_exists($classPath)) {
            $this->output->error("No class path found for $classPath");

            return self::FAILURE;
        }

        $modelInstance = new $classPath;
        $modelInfo     = ModelInfo::forModel($modelInstance);
        $modelName     = class_basename($classPath);

        $hiddenKeys = array_merge(
            $modelInstance->instanceHidden ?? [],
            [
                'id',
                'created_at',
                'updated_at',
            ]
        );

        /** @var Model&CreatesInstances $modelInstance */

        $fillableAttributes = $modelInfo->attributes->filter(
            fn(Attribute $attribute) => in_array($attribute->name, $modelInstance->getFillable())
        );

        $fillableAttributes = $fillableAttributes->filter(
            fn(Attribute $attribute) => ! in_array($attribute->name, $hiddenKeys)
        );

        /*$requiredAttributes = $modelInfo->attributes->filter(
            fn(Attribute $attribute) => !$attribute->nullable && $attribute->default !== null
        );*/

        // TODO: Get required relations.

        $relationAttributes = [];
        foreach ($modelInfo->attributes as $attribute) {
            if ( ! str_ends_with($attribute->name, '_id')) {
                continue;
            }

            $relation = substr($attribute->name, 0, -3);

            // TODO: Can relation attributes also have other names?
            if (method_exists($modelInstance, $relation) || method_exists($modelInstance, "{$relation}s")) {
                $relationAttributes[$relation] = $attribute;
            }
        }

        $relationAttributes = collect($relationAttributes);

        $requiredRelationAttributes = $relationAttributes->filter(
            fn(Attribute $attribute) => ! $attribute->nullable
        );

        $fillableAttributes->each(function (Attribute $attribute) use (&$modelInstance, $hiddenKeys) {
            $key                 = $attribute->name;
            $value               = $this->ask("Set value for $key", $attribute->default);
            $modelInstance->$key = $value;
        });

        // TODO: Ask required and optional attributes.

        $this->info($modelInstance->toJson(JSON_PRETTY_PRINT));

        if ($this->confirm("Create a $modelName", true)) {
            $modelInstance->save();
        } else {
            $this->output->error('Aborted instance creation');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
