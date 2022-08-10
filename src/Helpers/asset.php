<?php

if(!function_exists('asset')) {
    function asset(string $path): string
    {
        return app()->basePath('resources/'.$path);
    }
}
