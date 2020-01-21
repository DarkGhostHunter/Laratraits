<?php

namespace DarkGhostHunter\Laratraits;

use Illuminate\Mail\Markdown;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use const PHP_EOL;

/**
 * Trait RendersFromMarkdown
 * ---
 * This trait allows a class to hold markdown text and parse it to HTML. This can be used with the
 * Htmlable interface to automatically transform the given class into HTML. This trait will not
 * fail if the text to parse is empty; you should validate the data of the class beforehand.
 *
 * @package DarkGhostHunter\Laratraits
 *
 * @see \Illuminate\Contracts\Support\Htmlable
 */
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
