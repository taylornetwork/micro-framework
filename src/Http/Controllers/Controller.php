<?php

namespace TaylorNetwork\MicroFramework\Http\Controllers;

use TaylorNetwork\MicroFramework\Contracts\Http\Controller as ControllerContract;
use TaylorNetwork\MicroFramework\Contracts\Views\Page;
use TaylorNetwork\MicroFramework\Http\Responses\Response;

abstract class Controller implements ControllerContract
{
    public function page(string $name, array $data = []): Page
    {

    }
}
