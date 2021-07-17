<?php
/**
 * RegisterBladeExtensions
 *
 * This traits allows you to register multiple Blade extensions in just a few arrays.
 * The Blade compiler uses **callables** for directives and the custom if statements
 * so you will need to use public static methods and them as class@method notation.
 *
 *     protected $directives = [
 *         'package-alert' => 'App\Blade\Directives\AlertComponent@alert',
 *         'status-now' => 'App\Blade\Directives\Status@now',
 *     ]
 *
 *     protected $if = [
 *         'cloud' => 'App\Blade\Conditions\Cloud',
 *         'local' => 'App\Blade\Conditions\Local@condition',
 *     ]
 *
 *     protected $include = [
 *         'input' => 'includes.input'
 *         'authavatar' => 'includes.auth_avatar'
 *     ]
 *
 *     public function boot()
 *     {
 *         $this->registerBladeExtensions();
 *
 *         // ...
 *     }
 *
 * @see https://laravel.com/docs/blade#extending-blade
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

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

trait RegisterBladeExtensions
{
    /**
     * Register directives, includes and if statements in Blade.
     *
     * @return void
     */
    protected function registerBladeExtensions(): void
    {
        $compiler = Blade::getFacadeRoot();

        foreach ($this->directives ?? [] as $name => $handler) {
            $compiler->directive($name, Str::parseCallback($handler));
        }

        foreach ($this->if ?? [] as $name => $handler) {
            $compiler->if($name, Str::parseCallback($handler));
        }

        foreach ($this->include ?? [] as $name => $view) {
            $compiler->include($name, $view);
        }
    }
}
