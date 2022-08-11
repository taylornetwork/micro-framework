<?php

namespace TaylorNetwork\MicroFramework\Contracts\Views;

interface Page
{
    public function __construct(?string $name = null, array $data = []);

    public function share(array $data = []): static;

    public function render(): ?string;
}
