<?php

namespace TaylorNetwork\MicroFramework\Controllers;

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use TaylorNetwork\MicroFramework\Builders\PageBuilder;
use TaylorNetwork\MicroFramework\Contracts\Controller as ControllerContract;

abstract class Controller implements ControllerContract
{
    public function response(string $type, string $body): ResponseInterface
    {
        $type = strtolower($type);
        return Response::$type($body);
    }

    public function html(string $html): ResponseInterface
    {
        return $this->response('html', $html);
    }

    public function xml(string $xml): ResponseInterface
    {
        return $this->response('xml', $xml);
    }


    public function text(string $text): ResponseInterface
    {
        return $this->response('plaintext', $text);
    }

    public function json(mixed $jsonable): ResponseInterface
    {
        return $this->response('json', json_encode($jsonable));
    }


    public function redirect(string $location): ResponseInterface
    {
        return new Response(status: 302, headers: [
            'Location' => $location
        ]);
    }

    public function page(string $pageName, array $replacements = []): ResponseInterface
    {
        return $this->html($this->pageHtml($pageName, $replacements));
    }

    public function pageHtml(string $pageName, array $replacements = []): string
    {
        return $this->pageBuilder()->usePage($pageName)->setReplacements($replacements)->getRenderedHtml();
    }

    public function pageBuilder(): PageBuilder
    {
        return new PageBuilder();
    }

}
