<?php
/**
 * PublishesMigrations
 *
 * The following traits is intended for package developers. This will will find
 * all migrations properly named located in the default `database/migrations`
 * directory, and it will proceed to register each of them as publishable.
 *
 *     public function boot(): void
 *     {
 *         $this->publishMigrations();
 *
 *         // ...
 *     }
 *
 * The developer will be able to publish these files using the "migration" tag.
 *
 * Migrations names must follow this naming convention:
 *
 *     0000_00_00_000000_{snake_case_class_name}.php
 *
 * For example:
 *
 *     0000_00_00_000000_create_foo_table.php
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2021 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\ServiceProviders;

use Generator;
use Illuminate\Support\Str;

trait PublishesMigrations
{
    /**
     * Publishes migrations as assets.
     *
     * @return void
     */
    protected function publishMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $generator = function(): Generator {
                foreach ($this->app->make('files')->allFiles(__DIR__ . '/../database/migrations') as $file) {
                    if ($file->getExtension() === 'php' && Str::startsWith($file->getFilename(), '0000_00_00_000000')) {
                        yield $file->getPathname() => $this->app->databasePath(
                            'migrations/' .
                            now()->format('Y_m_d_His') .
                            Str::after($file->getFilename(), '0000_00_00_000000')
                        );
                    }
                }
            };

            $this->publishes(iterator_to_array($generator()), 'migrations');
        }
    }
}
