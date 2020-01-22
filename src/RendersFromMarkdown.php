<?php
/**
 * Throttles Requests
 *
 * This trait allows a class to hold markdown text and parse it to HTML. This can be used with the
 * Htmlable interface to automatically transform the given class into HTML. This trait will not
 * fail if the text to parse is empty; you should validate the data of the class beforehand.
 *
 * @see \Illuminate\Contracts\Support\Htmlable
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

use Illuminate\Mail\Markdown;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use const PHP_EOL;

trait RendersFromMarkdown
{
    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->parseMarkdown()->toHtml();
    }

    /**
     * Transform the class instance into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Returns the markdown text to parse.
     *
     * @return string|mixed
     */
    abstract protected function getMarkdown();

    /**
     * Returns an HTML String instance containing HTML from a markdown text.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function parseMarkdown()
    {
        if (empty($text = $this->getMarkdown())) {
            return new HtmlString('');
        }

        // If the data is an array, or a collection, we will treat each array item as a line.
        if ($text instanceof Collection) {
            $text = $text->toArray();
        }

        if (is_array($text)) {
            $text = implode(PHP_EOL, $text);
        }

        return Markdown::parse((string)$text);
    }
}
