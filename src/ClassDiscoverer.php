<?php
/**
 * Class Discoverer
 *
 * This is the object-version of the DiscoverEvents class, but modified to handle only class names
 * and filter these classes by method or implementation. It will discover classes recursively in
 * the path you have set relative to a base path, and use the lookup directories as namespaces.
 *
 * @see \Illuminate\Foundation\Events\DiscoverEvents
 *
 * MIT License
 *
 * Copyright (c) Italo Israel Baeza Cabrera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits;

use SplFileInfo;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Foundation\Application;
use const DIRECTORY_SEPARATOR as DS;

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
        $this->basePath = trim($app->basePath(), DS);
    }

    /**
     * Path to look for inside a directory, relative to the base path.
     *
     * @param  string  $path
     * @return \DarkGhostHunter\Laratraits\ClassDiscoverer
     */
    public function path(string $path)
    {
        $this->path = $this->basePath . DS . trim(str_replace(['\\', '/'], DS, $path), DS);

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
        $class = trim(Str::replaceFirst($this->basePath, '', $file->getRealPath()), DS);

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
            if (strpos($method->name, $this->filter['value']) === 0) {
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
