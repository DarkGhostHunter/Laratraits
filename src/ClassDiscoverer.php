<?php

namespace DarkGhostHunter\Laratraits;

use SplFileInfo;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

/**
 * Class ClassDiscoverer
 * ---
 * This is a blatant copy of the DiscoverEvents, but modified to handle only class names.
 *
 * @see \Illuminate\Foundation\Events\DiscoverEvents
 * @package DarkGhostHunter\Laratraits
 */
class ClassDiscoverer
{
    /**
     * Get a list of the classes based on the files inside a directory.
     *
     * @param  string  $path  The path to look for, relative to the $basePath.
     * @param  string  $basePath  The base path. If null, it will use the application base path.
     * @param  string|null  $method  Optional filter by a public method in each class.
     * @return \Illuminate\Support\Collection
     */
    public static function from(string $path, string $basePath = null, string $method = null)
    {
        $basePath = $basePath ?? base_path();

        return collect(static::getFiles(
            (new Finder)->files()->in($path), $basePath, $method
        ))->mapToDictionary(function ($path, $class) {
            return [$path => $class];
        });
    }

    /**
     * Get all of the files and parse the class if it exists.
     *
     * @param  iterable  $files
     * @param  string  $basePath
     * @param  string|null  $methodName
     * @return array
     */
    protected static function getFiles(iterable $files, string $basePath, string $methodName = null)
    {
        $classes = [];

        foreach ($files as $file) {
            try {
                $class = new ReflectionClass(
                    static::classFromFile($file, $basePath)
                );
            }
            catch (ReflectionException $e) {
                continue;
            }

            if (! $class->isInstantiable()) {
                continue;
            }

            if (! $methodName) {
                $classes[] = $class->name;
                continue;
            }

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (! Str::is($methodName . '*', $method->name)) {
                    continue;
                }

                $classes[] = $class->name;
            }
        }

        return $classes;
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $basePath
     * @return string
     */
    protected static function classFromFile(SplFileInfo $file, $basePath)
    {
        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())) . '\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }
}
