<?php

namespace TaylorNetwork\MicroFramework\Core;

use TaylorNetwork\MicroFramework\Core\Exceptions\ApplicationException;
use FrameworkX\App;
use FrameworkX\Container;
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
    public static string $frameworkNamespace = 'TaylorNetwork\\MicroFramework\\';

    /**
     * The application instance.
     *
     * @var Application
     */
    protected static Application $instance;

    /**
     * Base path of the framework.
     *
     * @var string
     */
    protected string $frameworkPath;

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
     * Relative path from basePath to appPath.
     *
     * @var string
     */
    protected string $appSegment;

    /**
     * The loaded service providers.
     *
     * @var array<ServiceProvider>
     */
    protected array $providers = [];

    /**
     * Has the Application initialized?
     *
     * @var bool
     */
    private bool $initialized = false;

    /**
     * @throws ReflectionException
     * @throws ApplicationException
     */
    public function __construct(
        protected ?ContainerInterface $container = null,
        protected ?Container $appContainer = null,
        protected array $providerList = [],
        protected array $classAliases = [],
        ?callable $preInitCallback = null,
        array $arguments = [],
    ) {
        $this->frameworkPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..']));

        if($preInitCallback) {
            $preInitCallback($this);
        }

        $this->appContainer ??= new Container($this->container ?? []);

        parent::__construct($this->appContainer, ...$arguments);

        $this->basePath ??= realpath('./..');
        $this->appPath ??= $this->appPath();

        $this->discoverFrameworkProviders();
        $this->discoverApplicationProviders();
        $this->bootProviders();
        $this->registerProviders();

        $this->setInstance();
        $this->initialized = true;
    }

    /**
     * @throws ApplicationException
     */
    private function throwIfNotInitialized(): void
    {
        if(!$this->initialized) {
            throw new ApplicationException('Application must be initialized first.');
        }
    }

    /**
     * @throws ApplicationException
     */
    private function throwIfInitialized(): void
    {
        if(!$this->initialized) {
            throw new ApplicationException('Can only be called before application initialization.');
        }
    }

    /**
     * @throws ApplicationException
     */
    public function getAppContainer(): Container
    {
        $this->throwIfNotInitialized();
        return $this->appContainer;
    }

    /**
     * @throws ApplicationException
     */
    public function getContainer(): ContainerInterface
    {
        $this->throwIfNotInitialized();
        return $this->container;
    }

    /**
     * @throws ApplicationException
     */
    public function make(string $abstract)
    {
        $this->throwIfNotInitialized();
        if(array_key_exists($abstract, $this->classAliases)) {
            return new $this->classAliases[$abstract];
        }
        return null;
    }

    /**
     * @throws ApplicationException
     */
    public function overrideContainer(ContainerInterface $container): static
    {
        return $this->overrideProp('container', $container);
    }

    /**
     * @throws ApplicationException
     */
    public function overrideAppContainer(Container $appContainer): static
    {
        return $this->overrideProp('appContainer', $appContainer);
    }

    /**
     * @throws ApplicationException
     */
    public function overrideBasePath(string $basePath): static
    {
        return $this->overrideProp('basePath', $basePath);
    }

    /**
     * @throws ApplicationException
     */
    public function overrideAppPath(string $appPath): static
    {
        return $this->overrideProp('appPath', $appPath);
    }

    /**
     * @throws ApplicationException
     */
    public function overrideAppSegment(string $appSegment): static
    {
        return $this->overrideProp('appSegment', $appSegment);
    }

    /**
     * @throws ApplicationException
     */
    private function overrideProp(string $property, mixed $value): static
    {
        $this->throwIfInitialized();
        $this->$property = $value;
        return $this;
    }

    /**
     * Discover service providers.
     *
     * @param string $globPath
     * @param string $globPattern
     * @return void
     */
    private function discoverProviders(string $globPath, string $globPattern = '*'): void
    {
        foreach(glob($this->normalizePath($globPath.'/'.$globPattern)) as $provider) {
            $class = $this->resolveClassFromPath($provider);
            if(!in_array($class, $this->providerList)) {
                $this->providerList[] = $class;
            }
        }
    }

    protected function discoverFrameworkProviders(): void
    {
        $this->discoverProviders($this->frameworkPath('Providers'));
    }

    /**
     * @throws ApplicationException
     */
    protected function discoverApplicationProviders(): void
    {
        $this->discoverProviders($this->appPath('Providers'));
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
        return $this->path('base', $path);
    }

    /**
     * Build a path by prefixing the appPath.
     *
     * @param ?string $path
     * @return string
     * @throws ApplicationException
     */
    public function appPath(?string $path = null): string
    {
        if(isset($this->appPath)) {
            return $this->path('app', $path);
        }

        if(isset($this->appSegment)) {
            return $this->basePath($this->appSegment.($path === null ? null : DIRECTORY_SEPARATOR.$path));
        }

        $composer = json_decode(file_get_contents($this->basePath.DIRECTORY_SEPARATOR.'composer.json'), true);
        $psr4 = $composer['autoload']['psr-4'] ?? [];

        if($psr4 !== []) {
            $this->appSegment = array_values($psr4)[0];
            return $this->appPath($path);
        }

        throw new ApplicationException('appPath could not be found automatically.');
    }

    public function frameworkPath(?string $path = null): string
    {
        return $this->path('framework', $path);
    }

    /**
     * Normalize and collapse directory separators.
     *
     * @param ?string $path
     * @return ?string
     */
    public function normalizePath(?string $path): ?string
    {
        if(!$path) {
            return null;
        }

        $path = preg_replace('#(/\\\\)+#', DIRECTORY_SEPARATOR, $path);
        return str_ends_with($path, DIRECTORY_SEPARATOR) ? substr($path, 0, strlen($path)-1) : $path;
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
     * @param ?string $baseNamespace
     * @param bool $endingSlash
     * @param bool $startingSlash
     * @return string
     */
    public function buildNamespaceFromArray(array $segments, string $baseNamespace = null, bool $endingSlash = true, bool $startingSlash = false): string
    {
        $baseNamespace ??= static::$frameworkNamespace;
        $namespace = ($startingSlash ? '\\' : '').$baseNamespace;
        foreach($segments as $segment) {
            $namespace .= (Str::endsWith($namespace, '\\') ? '' : '\\').$segment;
        }
        return $namespace.($endingSlash ? '\\' : '');
    }

    /**
     * @throws ApplicationException
     */
    public function map(array $methods, string $route, $handler, ...$handlers): void
    {
        $this->throwIfNotInitialized();
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

    protected function path(string $key, ?string $path = null): string
    {
        $key = str_replace('Path', '', $key).'Path';
        $path = $this->normalizePath($path);

        if($path && !str_starts_with($path, DIRECTORY_SEPARATOR)) {
            $path = DIRECTORY_SEPARATOR.$path;
        }

        return $this->normalizePath($this->$key.$path);
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
