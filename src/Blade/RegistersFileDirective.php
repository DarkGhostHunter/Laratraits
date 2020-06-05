<?php
/**
 * RegistersFileDirective
 *
 * This is a very nice way to register a directive by using a raw PHP file contents using the snake_name
 * from the class name. Use this in your directive class, and register it using 'Class@method' notation
 * and USE "$expression" INSIDE YOUR DIRECTIVE FILE. This is because it replaces it after registration.
 *
 *     Blade::directive('datetime', 'App\Blade\FormatDirective::register');
 *
 * Then, in put something like this in your directive file "resources\views\directives\format_directive.php":
 *
 *     <?php
 *
 *     echo $expression->format('m/d/Y H:i');
 *
 * This trait will read the file and evaluate the contents automatically, close any PHP tag missing,
 * and returning it to the Blade compiler seamlessly, making the creation of directives very easy.
 *
 * You can also use a single trait for a class with multiple file directives.
 *
 *     Blade::directive('simpleformat', 'App\Blade\FormatDirective::simpleFormat');
 *     Blade::directive('isoformat', 'App\Blade\FormatDirective::isoFormat');
 *
 *     public static function simpleFormat($expression)
 *     {
 *         return static::register($expression, 'app/blade/directives/simple_format.php');
 *     }
 *
 *     public static function isoFormat($expression)
 *     {
 *         return static::register($expression, 'resources/views/directives/iso_format.php');
 *     }
 *
 * ---
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

namespace DarkGhostHunter\Laratraits\Blade;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use const DIRECTORY_SEPARATOR as DS;

trait RegistersFileDirective
{
    /**
     * Registers the directive.
     *
     * @param  mixed  $expression
     * @param  string|null  $path
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function register($expression = null, string $path = null)
    {
        $contents = Str::finish(stripcslashes(static::getDirectiveContents($path)), ' ?>');

        if ($expression) {
            return str_replace('$expression', $expression, $contents);
        }

        return $contents;
    }

    /**
     * Returns the file directive contents.
     *
     * @param  string|null  $path
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected static function getDirectiveContents(?string $path = null)
    {
        // If the developer has returned a path for the directive, we will use just that.
        // Otherwise, we will just cycle between a list of predefined paths where the
        // directive should be, and throw an exception if we can't find anything.
        if ($path) {
            return File::get($path);
        }

        $file = Str::finish($path ?? Str::snake(class_basename(static::class)), '.php');

        $dir = dirname((new \ReflectionClass(static::class))->getFileName());

        $paths = [
            resource_path('views' . DS . 'directives' . DS . $file),
            $dir . DS . $file,
            $dir . DS . 'directives' . DS . $file,
            $dir . DS . 'directives' . DS . $file,
            $dir . DS . 'Directive' . DS . $file,
        ];

        foreach ($paths as $automaticPath) {
            if (File::exists($automaticPath)) {
                return File::get($automaticPath);
            }
        }

        throw new FileNotFoundException(
            'The class ' . static::class . ' has no file [' . $file . '] to register as directive.'
        );
    }
}
