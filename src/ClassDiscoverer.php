<?php

namespace DarkGhostHunter\Laratraits;

use SplFileInfo;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Illuminate\Database\Eloquent\Collection;
use const DIRECTORY_SEPARATOR;

/**
 * Class ClassDiscoverer
 * ---
 * This is the object-version of the DiscoverEvents class, but modified to handle only class names and filter
 * these classes by method or implementation. It needs
 *
 * @see \Illuminate\Foundation\Events\DiscoverEvents
 * @package DarkGhostHunter\Laratraits
 */
class ClassDiscoverer
{
    /**
     * The Base Path as root namespace to look up.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Base Namespace for the classes to discover.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Directory path to look inside.
     *
     * @var string
     */
    protected $path;

    /**
     * Filtering mechanism
     *
     * @var array[]
     * @example [ 'filter' => 'interface', 'value' => \Illuminate\Contracts\Support\Jsonable::class ]
     */
    protected $filter;

    /**
     * Creates a new ClassDiscoverer instance.
     *
     * @param  string  $basePath
     * @param  string|null  $namespace
     */
    public function __construct(string $basePath = null, string $namespace = null)
    {
        $app = app();

        $this->basePath = trim($basePath ?? $app->basePath(), DIRECTORY_SEPARATOR);
        $this->namespace = $namespace ?? $app->getNamespace();
        $this->path = $app->path();
    }

    /**
     * Path to look for inside a directory.
     *
     * @param  string  $path
     * @return \DarkGhostHunter\Laratraits\ClassDiscoverer
     */
    public function path(string $path)
    {
        $this->path = $this->basePath . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Filters the class list by only those containing a public method.
     *
     * @param  string  $method
     * @return \DarkGhostHunter\Laratraits\ClassDiscoverer
     */
    public function filterByMethod(string $method)
    {
        $this->filter = [
            'type'  => 'method',
            'value' => $method,
        ];

        return $this;
    }

    /**
     * Filter the class list by only those implementing a given interface.
     *
     * @param  string  $interface
     * @return $this
     */
    public function filterByInterface(string $interface)
    {
        if (! interface_exists($interface)) {
            throw new InvalidArgumentException("The interface [$interface] has not been declared.");
        }

        $this->filter = [
            'type'  => 'interface',
            'value' => $interface,
        ];

        return $this;
    }

    /**
     * Returns the Class names.
     *
     * @return \Illuminate\Support\Collection
     */
    public function discover()
    {
        return collect($this->getFiles())
            ->filter([$this, 'filterClasses'])
            ->when($this->filter, function (Collection $classes, $filter) {
                return $classes->filter([
                    $this, $filter['type'] === 'interface' ? 'filterInterface' : 'filterMethod'
                ]);
            });
    }

    /**
     * Get the files for the given path
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getFiles()
    {
        return (new Finder)->files()->in($this->path);
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  \SplFileInfo  $file
     * @return string
     */
    protected function classFromFile(SplFileInfo $file)
    {
        $class = trim(Str::replaceFirst($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename($this->path)) . '\\'],
            ['\\', $this->namespace],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }

    /**
     * Returns a Reflection Class from the SplFileInfo File
     *
     * @param  \SplFileInfo  $file
     * @return null|\ReflectionClass
     */
    protected function filterClasses(SplFileInfo $file)
    {
        try {
            $class = new ReflectionClass($this->classFromFile($file));
        } catch (ReflectionException $e) {
            return null;
        }

        if (! $class->isInstantiable()) {
            return null;
        }

        return $class;
    }

    /**
     * Filter each class by a method name.
     *
     * @param  \ReflectionClass  $class
     * @return null|\ReflectionClass
     */
    protected function filterMethod(ReflectionClass $class)
    {
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (Str::is($this->filter['value'] . '*', $method->name)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Filter each class by a given implementation.
     *
     * @param  \ReflectionClass  $class
     * @return null|\ReflectionClass
     */
    protected function filterInterface(ReflectionClass $class)
    {
        if (in_array($this->filter['value'], $class->getInterfaceNames(), true)) {
            return $class;
        }

        return null;
    }
}
