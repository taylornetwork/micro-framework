<?php

namespace TaylorNetwork\MicroFramework\Contracts\Http;

use TaylorNetwork\MicroFramework\Contracts\Views\Page;

interface Controller
{
    public function page(string $name, array $data = []): Page;
}
