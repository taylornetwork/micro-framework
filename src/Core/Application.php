<?php

namespace TaylorNetwork\MicroFramework\Core;

use FrameworkX\App;
use FrameworkX\Container;
use Illuminate\Support\Str;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use TaylorNetwork\MicroFramework\Contracts\ServiceProvider;

class Application extends App
{
    /**
     * Base PSR-4 namespace.
     *
     * @var string
     */
    public static string $namespace = 'TaylorNetwork\\MicroFramework\\';

    /**
     * The application instance.
     *
     * @var Application
     */
    protected static Application $instance;

    /**
     * The base project path.
     *
     * @var string
     */
    protected string $basePath;

    /**
     * The application base path.
     *
     * @var string
     */
    protected string $appPath;

    /**
     * The loaded service providers.
     *
     * @var array<ServiceProvider>
     */
    protected array $providers = [];


    public function __construct(
        protected ?ContainerInterface $container = null,
        protected ?Container $appContainer = null,
        protected array $providerList = [],
        protected array $classAliases = [],
        array $arguments = []
    ) {
        $this->appContainer ??= new Container($this->container ?? []);

        parent::__construct($this->appContainer, ...$arguments);

        $this->basePath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));
        $this->appPath = $this->appPath();

        $this->discoverProviders();
        $this->bootProviders();
        $this->registerProviders();

        $this->setInstance();
    }

    public function getAppContainer(): Container
    {
        return $this->appContainer;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function make(string $abstract)
    {
        if(array_key_exists($abstract, $this->classAliases)) {
            return new $this->classAliases[$abstract];
        }
        return null;
    }

    /**
     * Discover and boot service providers.
     *
     * @return void
     */
    protected function discoverProviders(): void
    {
        foreach(glob($this->appPath('Providers/*')) as $provider) {
            $class = $this->resolveClassFromPath($provider);
            if(!in_array($class, $this->providerList)) {
                $this->providerList[] = $class;
            }
        }
    }

    /**
     * Instantiate and boot providers.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function bootProviders(): void
    {
        foreach($this->providerList as $provider) {
            $reflection = new ReflectionClass($provider);

            if($reflection->isInstantiable()) {
                $instance = new $provider($this);
                $instance->boot();
                $this->providers[] = $instance;
            }
        }
    }

    /**
     * Register all service providers.
     *
     * @return void
     */
    protected function registerProviders(): void
    {
        foreach($this->providers as $provider) {
            $provider->register();
        }
    }

    /**
     * Set the app instance if not set.
     *
     * @return void
     */
    protected function setInstance(): void
    {
        if(!isset(static::$instance)) {
            static::$instance = $this;
        }
    }

    /**
     * Build a path by prefixing the basePath.
     *
     * @param ?string $path
     * @return string
     */
    public function basePath(?string $path = null): string
    {
        if($path !== null) {
            $path = Str::start($path, DIRECTORY_SEPARATOR);
        }

        return $this->normalizePath($this->basePath.$path);
    }

    /**
     * Build a path by prefixing the appPath.
     *
     * @param ?string $path
     * @return string
     */
    public function appPath(?string $path = null): string
    {
        return $this->basePath('src'.($path === null ? null : DIRECTORY_SEPARATOR.$path));
    }

    /**
     * Normalize and collapse directory separators.
     *
     * @param string $path
     * @return string
     */
    public function normalizePath(string $path): string
    {
        return preg_replace('#(/\\\\)+#', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Resolve a PSR-4 class from a path.
     *
     * @param string $classPath
     * @return string
     */
    public function resolveClassFromPath(string $classPath): string
    {
        $className = str_replace('.php', '', class_basename($classPath));

        // Remove the class name and final separator from the path
        $classPath = str_replace(DIRECTORY_SEPARATOR.$className.'.php', '', $this->normalizePath($classPath));

        // Get the relative path to the application
        $relativePath = str_replace($this->appPath.DIRECTORY_SEPARATOR, '', $classPath);

        // Get the path segments to be added to the namespace
        $pathSegments = explode(DIRECTORY_SEPARATOR, $relativePath);

        return $this->buildNamespaceFromArray($pathSegments).$className;
    }

    /**
     * Build a namespace given path segments.
     *
     * @param array<string> $segments
     * @param bool $endingSlash
     * @param bool $startingSlash
     * @return string
     */
    public function buildNamespaceFromArray(array $segments, bool $endingSlash = true, bool $startingSlash = false): string
    {
        $namespace = ($startingSlash ? '\\' : '').static::$namespace;
        foreach($segments as $segment) {
            $namespace .= (Str::endsWith($namespace, '\\') ? '' : '\\').$segment;
        }
        return $namespace.($endingSlash ? '\\' : '');
    }

    public function map(array $methods, string $route, $handler, ...$handlers): void
    {
        // Convert a [class-string, method] array to a callable for FrameworkX
        if(is_array($handler) && !is_callable($handler)) {
            if(gettype($handler[0]) === 'string' && class_exists($handler[0])) {
                $callable = [new $handler[0], $handler[1]];
                if(is_callable($callable)) {
                    $handler = $callable;
                }
            }
        }

        parent::map($methods, $route, $handler, ...$handlers);
    }


    /**
     * Get the application instance.
     *
     * @return ?Application
     */
    public static function getInstance(): ?Application
    {
        return static::$instance;
    }
}
