<?php

use TaylorNetwork\MicroFramework\Core\Exceptions\ApplicationException;

if(!function_exists('app_path')) {
    /**
     * @throws ApplicationException
     */
    function app_path(?string $path = null): string
    {
        return app()->appPath($path);
    }
}
