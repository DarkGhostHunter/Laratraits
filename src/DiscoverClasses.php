<?php
/**
 * Discover Classes
 *
 * This class allows to spy inside a directory and check all PHP files containing classes a-la PSR-4.
 * You will receive a Collection made of all the instantiable classes, optionally filtered by a
 * method name or implementation interface. You will receive a collection of the classes.
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

trait DiscoverClasses
{
    /**
     * Discover instantiable classes from a given path.
     *
     * @param  string  $path  The Path to discover. Defaults to the
     * @param  string|null  $methodOrInterface The Method name or Interface to filter the classes.
     * @return \Illuminate\Support\Collection  A collection of filtered class names.
     */
    public function discover(string $path, string $methodOrInterface = null)
    {
        $discoverer = app(ClassDiscoverer::class)->path($path);

        if ($methodOrInterface) {
            if (interface_exists($methodOrInterface)) {
                $discoverer->filterByInterface($methodOrInterface);
            } else {
                $discoverer->filterByMethod($methodOrInterface);
            }
        }

        return $discoverer->discover();
    }
}
