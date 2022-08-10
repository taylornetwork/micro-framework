<?php

namespace TaylorNetwork\MicroFramework\Providers;

use TaylorNetwork\MicroFramework\Contracts\Abstract\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach (glob($this->app->appPath('Helpers/*')) as $helper) {
            include_once $helper;
        }
    }
}
