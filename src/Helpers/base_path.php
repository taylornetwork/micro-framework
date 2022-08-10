<?php

if(!function_exists('base_path')) {
    function base_path(?string $path = null): string
    {
        return app()->basePath($path);
    }
}
