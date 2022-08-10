<?php

namespace TaylorNetwork\MicroFramework\Contracts\Abstract;

use TaylorNetwork\MicroFramework\Contracts\ServiceProvider as ServiceProviderContract;
use TaylorNetwork\MicroFramework\Core\Application;

abstract class ServiceProvider implements ServiceProviderContract
{
    public function __construct(
        protected Application $app
    ) {}

    public function boot(): void
    {
        //
    }

}
