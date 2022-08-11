<?php

namespace TaylorNetwork\MicroFramework\Contracts\Http;

use TaylorNetwork\MicroFramework\Contracts\Views\Page;

interface Controller
{
    public function response(string $type, string $body): HttpResponse;

    public function html(string $html): HttpResponse;

    public function xml(string $xml): HttpResponse;

    public function text(string $text): HttpResponse;

    public function json(mixed $jsonable): HttpResponse;

    public function redirect(string $location): HttpResponse;

    public function page(string $name, array $data = []): Page;
}
