<?php

namespace Saggre\LaravelModelInstance\Data;

class ModelInstanceAttributeInfo
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $required,
        public mixed $default,
    ) {
    }
}
