<?php

namespace TaylorNetwork\MicroFramework\Controllers;

use Fig\Http\Message\StatusCodeInterface;
use TaylorNetwork\MicroFramework\Contracts\Http\Controller as ControllerContract;
use TaylorNetwork\MicroFramework\Contracts\Http\HttpResponse;
use TaylorNetwork\MicroFramework\Contracts\Http\Status;
use TaylorNetwork\MicroFramework\Contracts\Views\Page;
use TaylorNetwork\MicroFramework\Http\Responses\Response;

abstract class Controller implements ControllerContract
{
    public function response(string $type, string $body): HttpResponse
    {
        $type = strtolower($type);
        return Response::$type($body);
    }

    public function html(string $html): HttpResponse
    {
        return $this->response('html', $html);
    }

    public function xml(string $xml): HttpResponse
    {
        return $this->response('xml', $xml);
    }


    public function text(string $text): HttpResponse
    {
        return $this->response('plaintext', $text);
    }

    public function json(mixed $jsonable): HttpResponse
    {
        return $this->response('json', json_encode($jsonable));
    }


    public function redirect(string $location): HttpResponse
    {
        return new Response(
            status: StatusCodeInterface::STATUS_FOUND,
            headers: [
                'Location' => $location
            ]
        );
    }

    public function page(string $name, array $data = []): Page
    {
        // TODO: Implement page() method.
    }
}
