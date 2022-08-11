<?php

namespace TaylorNetwork\MicroFramework\Http\Responses;

use Fig\Http\Message\StatusCodeInterface;
use RingCentral\Psr7\Response as Psr7Response;
use TaylorNetwork\MicroFramework\Contracts\Http\HttpResponse;

class Response extends Psr7Response implements HttpResponse, StatusCodeInterface
{

}
