<?php
/**
 * FiresItself
 *
 * This trait allows an Event to be fired using a static method than rather a helper.
 *
 *     NewSubscriptionEvent::fire($subscription, $client);
 *
 * You can also fire the event and halt when a listener returns a response.
 *
 *     NewSubscriptionEvent::fireHalted($subscription, $client);
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

namespace DarkGhostHunter\Laratraits;

trait FiresItself
{
    /**
     * Fires this event.
     *
     * @param  mixed  ...$args
     * @return null|array
     */
    public static function fire(...$args): ?array
    {
        return app('events')->dispatch(new static(...$args));
    }

    /**
     * Fires this event and halts if a listener returns a response.
     *
     * @param  mixed  ...$args
     * @return null|array
     */
    public static function fireHalted(...$args): ?array
    {
        return app('events')->dispatch(new static(...$args), [], true);
    }
}
