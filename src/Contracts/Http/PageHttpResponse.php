<?php

namespace TaylorNetwork\MicroFramework\Contracts\Http;

use TaylorNetwork\MicroFramework\Contracts\Views\Page;

interface PageHttpResponse extends HttpResponse
{
    public static function page(Page $page): static;
}
