<?php

namespace TaylorNetwork\MicroFramework\Contracts;

use Psr\Http\Message\ResponseInterface;
use TaylorNetwork\MicroFramework\Builders\PageBuilder;

interface Controller
{
    public function response(string $type, string $body): ResponseInterface;

    public function html(string $html): ResponseInterface;

    public function xml(string $xml): ResponseInterface;

    public function text(string $text): ResponseInterface;

    public function json(mixed $jsonable): ResponseInterface;

    public function redirect(string $location): ResponseInterface;

    public function page(string $pageName, array $replacements = []): ResponseInterface;

    public function pageHtml(string $pageName, array $replacements = []): string;

    public function pageBuilder(): PageBuilder;
}
