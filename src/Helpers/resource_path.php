<?php

if(!function_exists('resource_path')) {
    function resource_path(string $path): string
    {
        return app()->basePath('resources/'.$path);
    }
}
