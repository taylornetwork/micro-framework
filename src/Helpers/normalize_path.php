<?php

if(!function_exists('normalize_path')) {
    function normalize_path(string $path): string
    {
        return app()->normalizePath($path);
    }
}
