<?php

namespace TaylorNetwork\MicroFramework\Contracts\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpResponse extends ResponseInterface
{
    public function html(string $html): static;

    public function xml(string $xml): static;

    public function text(string $text): static;

    public function json(mixed $jsonable): static;

    public function redirect(string $location): static;

    public static function make(...$arguments): static;
}
