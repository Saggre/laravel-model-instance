<?php

namespace Saggre\LaravelModelInstance\Data;

class ModelInstanceRelationInfo
{
    public function __construct(
        public string $name,
        public string $class,
        public bool $required,
    ) {
    }
}
