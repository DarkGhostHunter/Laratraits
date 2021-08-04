<?php
/**
 * SendsToHttp
 *
 * This trait allows an object to be sent to a given URL using the HTTP Client.
 *
 *     $class->send();
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

namespace DarkGhostHunter\Laratraits;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use LogicException;

trait SendsToHttp
{
    /**
     * Sends the current instance via an HTTP Request.
     *
     * @param  string|null  $url
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $url = null): Response
    {
        return $this->performHttpRequest(
            $this->httpRequestFactory(), $url ?? $this->url()
        );
    }

    /**
     * Performs the HTTP Request.
     *
     * @param  \Illuminate\Http\Client\PendingRequest  $request
     * @param  string  $url
     * @return \Illuminate\Http\Client\Response
     * @see  \Illuminate\Http\Client\PendingRequest
     */
    protected function performHttpRequest(PendingRequest $request, string $url): Response
    {
        return $request->post($url, $this->toHttp());
    }

    /**
     * Creates a HTTP Request object ready to be sent.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function httpRequestFactory(): PendingRequest
    {
        return Http::asJson();
    }

    /**
     * Return the HTTP endpoint where to send this object.
     *
     * @return string
     */
    protected function url(): string
    {
        if (property_exists($this, 'url')) {
            return $this->url;
        }

        throw new LogicException('This instance of ' . static::class . ' has no default URL to be sent to.');
    }

    /**
     * Get content that should be sent inside the Request.
     *
     * @return array|string|mixed
     */
    abstract protected function toHttp();
}
