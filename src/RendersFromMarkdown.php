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
     * @return string
     */
    protected function markdownText() : string
    {
        throw new \LogicException(
            'The class '. class_basename($this) . ' must point the markdown text to parse.'
        );
    }

    /**
     * Returns an HTML String instance containing HTML from a markdown text.
     *
     * @param  string|null  $location
     * @return \Illuminate\Support\HtmlString
     */
    protected function parseMarkdown(string $location = null)
    {
        $value = data_get($this, $location ?? $this->markdownText());

        if (empty($value)) {
            return new HtmlString('');
        }

        if (is_array($value) || $value instanceof Collection) {
            $value = implode(PHP_EOL, $value);
        }

        return Markdown::parse($value);
    }
}
