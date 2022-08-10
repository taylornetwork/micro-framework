<?php

namespace TaylorNetwork\MicroFramework\Builders;

use Closure;
use DI\Container;
use Psr\Container\ContainerInterface;
use ReflectionException;
use TaylorNetwork\MicroFramework\Core\Application;
use TaylorNetwork\MicroFramework\Core\Exceptions\ApplicationException;

class ApplicationBuilder
{
    protected ContainerInterface $container;

    protected array $arguments = [];

    protected array $providers = [];

    protected array $aliases = [];

    protected ?Closure $overrideCallback = null;

    public function withOverrideCallback(callable $callback): static
    {
        $this->overrideCallback = $callback;
        return $this;
    }

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

    /**
     * @throws ReflectionException
     * @throws ApplicationException
     */
    public function build(...$arguments): Application
    {
        return new Application(
            container: $this->container ?? new Container(),
            providerList: $this->providers,
            classAliases: $this->aliases,
            overrideCallback: $this->overrideCallback,
            arguments: array_merge($this->arguments, $arguments)
        );
    }
}
