<?php

namespace DarkGhostHunter\Laratraits;

use SplFileInfo;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Foundation\Application;
use const DIRECTORY_SEPARATOR;

/**
 * Class ClassDiscoverer
 * ---
 * This is the object-version of the DiscoverEvents class, but modified to handle only class names
 * and filter these classes by method or implementation. It will discover classes recursively in
 * the path you have set relative to a base path, and use the lookup directories as namespaces.
 *
 * @see \Illuminate\Foundation\Events\DiscoverEvents
 * @package DarkGhostHunter\Laratraits
 */
class ClassDiscoverer
{
    /**
     * The Base Path that will be discarded from all file paths.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Directory path to look inside.
     *
     * @var string
     */
    protected $path;

    /**
     * Filtering mechanisms
     *
     * @var array
     */
    protected $filter;

    /**
     * Creates a new ClassDiscoverer instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->path = $app->path();
        $this->basePath = trim($app->basePath(), DIRECTORY_SEPARATOR);
    }

    /**
     * Path to look for inside a directory, relative to the base path.
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
            'type'  => 'filterMethod',
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
            'type'  => 'filterInterface',
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
        $classes = collect($this->getFiles())
            ->map([$this, 'filterClasses'])
            ->filter();

        if ($this->filter) {
            $classes = $classes->filter([$this, $this->filter['type']]);
        }

        return $classes->map->name;
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
     * Returns a Reflection Class from the SplFileInfo File
     *
     * @param  \SplFileInfo  $file
     * @return null|\ReflectionClass
     */
    public function filterClasses(SplFileInfo $file)
    {
        try {
            $class = new ReflectionClass($this->classFromFile($file));
        }
        catch (ReflectionException $e) {
            return null;
        }

        if (! $class->isInstantiable()) {
            return null;
        }

        return $class;
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

        return ucfirst(Str::replaceLast('.php', '', $class));
    }

    /**
     * Filter each class by a method name.
     *
     * @param  \ReflectionClass  $class
     * @return bool
     */
    public function filterMethod(ReflectionClass $class)
    {

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (Str::is($this->filter['value'] . '*', $method->name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter each class by a given implementation.
     *
     * @param  \ReflectionClass  $class
     * @return bool
     */
    public function filterInterface(ReflectionClass $class)
    {
        if (in_array($this->filter['value'], $class->getInterfaceNames(), true)) {
            return true;
        }

        return false;
    }
}
