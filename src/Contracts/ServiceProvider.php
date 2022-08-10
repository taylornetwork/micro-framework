<?php

namespace TaylorNetwork\MicroFramework\Contracts;

use TaylorNetwork\MicroFramework\Core\Application;

interface ServiceProvider
{
    public function __construct(Application $app);

    public function register(): void;

    public function boot(): void;
}
