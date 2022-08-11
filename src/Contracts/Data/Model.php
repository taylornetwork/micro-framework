<?php

namespace TaylorNetwork\MicroFramework\Contracts\Data;

interface Model
{
    public function fill(array $attributes): static;

    public function getAttributes(): array;

    public function getAttribute(string $name): mixed;

    public function toArray(): array;

    public function addComputed(string $name, callable $closure): static;
}
