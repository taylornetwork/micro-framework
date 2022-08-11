<?php

namespace TaylorNetwork\MicroFramework\Models;

use TaylorNetwork\MicroFramework\Contracts\Data\Model as ModelContract;

abstract class Model implements ModelContract
{
    protected array $computed = [];

    public function __construct(
        protected array $attributes = []
    ) {}

    public function fill(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function addComputed(string $name, callable $closure): static
    {
        $this->computed[$name] = $closure;
        return $this;
    }

    public function getAttribute(string $name): mixed
    {
        if(array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if(array_key_exists($name, $this->computed)) {
            return $this->computed[$name]($this);
        }

        return null;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        $attributes = $this->getAttributes();
        $computed = [];

        if($attributes !== []) {
            foreach($this->computed as $key => $callable) {
                $computed[$key] = $callable($this);
            }
        }

        return array_merge($attributes, $computed);
    }
}
