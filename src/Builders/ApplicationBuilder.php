<?php

namespace TaylorNetwork\MicroFramework\Builders;

use DI\Container;
use Psr\Container\ContainerInterface;
use TaylorNetwork\MicroFramework\Core\Application;

class ApplicationBuilder
{
    protected ContainerInterface $container;

    protected array $arguments = [];

    protected array $providers = [];

    protected array $aliases = [];

    public function withContainer(ContainerInterface $container): static
    {
        $this->container = $container;
        return $this;
    }

    public function withArguments(array $arguments): static
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function withAliases(array $aliases): static
    {
        $this->aliases = $aliases;
        return $this;
    }

    public function withProviders(array $providers): static
    {
        $this->providers = $providers;
        return $this;
    }

    public function alias(string $alias, string $class): static
    {
        $this->aliases[$alias] = $class;
        return $this;
    }

    public function provider(string $provider): static
    {
        $this->providers[] = $provider;
        return $this;
    }

    public function build(...$arguments): Application
    {
        return new Application(
            container: $this->container ?? new Container(),
            providerList: $this->providers,
            classAliases: $this->aliases,
            arguments: array_merge($this->arguments, $arguments)
        );
    }
}
