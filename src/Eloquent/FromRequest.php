<?php
/**
 * FromRequest
 *
 * This trait allows the user to conveniently make or create a model by validating
 * the request and using that input, in one line.
 *
 *     Model::createFrom($request, ['url' => 'required|url']);
 *
 * You can also use `makeFrom()`, `fillFrom()` and `updateFrom()`.
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

namespace DarkGhostHunter\Laratraits\Eloquent;

trait FromRequest
{
    /**
     * Creates a model from a request.
     *
     * @param  \Illuminate\Http\Request|array $request
     * @param  array|null  $rules
     *
     * @return $this
     */
    public function createFrom($request, array $rules = null)
    {
        [$request, $rules] = $this->parseFromOptions($request, $rules);

        return $this->create($request->validate($rules));
    }

    /**
     * Makes a model instance from a request.
     *
     * @param  \Illuminate\Http\Request|array $request
     * @param  array|null  $rules
     *
     * @return $this
     */
    public function makeFrom($request, array $rules = null)
    {
        [$request, $rules] = $this->parseFromOptions($request, $rules);

        return $this->make($request->validate($rules));
    }

    /**
     * Fills a model from a request.
     *
     * @param  \Illuminate\Http\Request|array $request
     * @param  array|null  $rules
     *
     * @return $this
     */
    public function fillFrom($request, array $rules = null)
    {
        [$request, $rules] = $this->parseFromOptions($request, $rules);

        return $this->fill($request->validate($rules));
    }

    /**
     * Updates a model from a request.
     *
     * @param  \Illuminate\Http\Request|array $request
     * @param  array|null  $rules
     *
     * @return $this
     */
    public function updateFrom($request, array $rules = null)
    {
        [$request, $rules] = $this->parseFromOptions($request, $rules);

        return tap($this)->update($request->validate($rules));
    }

    /**
     * Parses the arguments from this trait options.
     *
     * @param  \Illuminate\Http\Request|array $request
     * @param  array|null  $rules
     *
     * @return array<\Illuminate\Http\Request,array>
     */
    protected function parseFromOptions($request, ?array $rules): array
    {
        return is_array($request)
            ? [request(), $request]
            : [$request, $rules];
    }
}
