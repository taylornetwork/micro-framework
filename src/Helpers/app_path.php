<?php

if(!function_exists('app_path')) {
    function app_path(?string $path = null): string
    {
        return app()->appPath($path);
    }
}
