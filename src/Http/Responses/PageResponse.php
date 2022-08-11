<?php

namespace TaylorNetwork\MicroFramework\Http\Responses;

use Fig\Http\Message\StatusCodeInterface;
use RingCentral\Psr7\Response;
use TaylorNetwork\MicroFramework\Contracts\Http\PageHttpResponse as PageResponseContract;
use TaylorNetwork\MicroFramework\Contracts\Views\Page;

class PageResponse extends Response implements PageResponseContract, StatusCodeInterface
{
    public static function page(Page $page): static
    {
        return new static(
            status: static::STATUS_OK,
            headers: [
                'Content-Type' => 'text/html; charset=utf-8'
            ],
            body: $page->render()
        );
    }
}
