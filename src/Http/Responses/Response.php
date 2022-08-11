<?php

namespace TaylorNetwork\MicroFramework\Http\Responses;

use Fig\Http\Message\StatusCodeInterface;
use RingCentral\Psr7\Response as Psr7Response;
use TaylorNetwork\MicroFramework\Contracts\Http\HttpResponse;

class Response extends Psr7Response implements HttpResponse, StatusCodeInterface
{
    public function html(string $html): static
    {
        return new static(
            status: static::STATUS_OK,
            headers: [
                'Content-Type' => 'text/html; charset=utf-8'
            ],
            body: $html
        );
    }

    public function xml(string $xml): static
    {
        return new static(
            status: static::STATUS_OK,
            headers: [
                'Content-Type' => 'application/xml'
            ],
            body: $xml
        );
    }

    public function text(string $text): static
    {
        return new static(
            status: self::STATUS_OK,
            headers: [
                'Content-Type' => 'text/plain; charset=utf-8'
            ],
            body: $text
        );
    }

    public function json(mixed $jsonable): static
    {
        return new static(
            status: self::STATUS_OK,
            headers: [
                'Content-Type' => 'application/json'
            ],
            body: json_encode($jsonable).PHP_EOL
        );
    }

    public function redirect(string $location): static
    {
        return new static(
            status: self::STATUS_FOUND,
            headers: [
                'Location' => $location
            ]
        );
    }

    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}
