<?php
/**
 * RegistersObservers
 *
 * This traits allows you to register multiple eloquent events by just issuing an array
 * into your EventServiceProvider, that your boot method should hook up manually:
 *
 *     protected $observers = [
 *         'App\User' => 'App\Observers\UserObserver',
 *         'App\Post' => ['App\Observers\PublicationObserver', 'App\Observers\HomeObserver']
 *     ]
 *
 *     public function boot()
 *     {
 *         $this->registerObservers();
 *
 *         // ...
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

namespace DarkGhostHunter\Laratraits\ServiceProviders;

trait RegistersObservers
{
    /**
     * Registers an array of handlers for a Eloquent event.
     *
     * @return void
     */
    protected function registerObservers()
    {
        foreach ($this->observers ?? [] as $model => $handlers) {
            foreach ((array)$handlers as $handler) {
                $model::observe($handler);
            }
        }
    }
}
