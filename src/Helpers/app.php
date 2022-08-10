<?php

use TaylorNetwork\MicroFramework\Core\Application;

if(!function_exists('app')) {
    function app(): Application
    {
        return Application::getInstance();
    }
}
