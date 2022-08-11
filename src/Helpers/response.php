<?php

use TaylorNetwork\MicroFramework\Http\Responses\Response;

if(!function_exists('response')) {
    function response(...$arguments): Response
    {
        return Response::make(...$arguments);
    }
}
