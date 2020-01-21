<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
use DarkGhostHunter\Laratraits\RendersFromMarkdown;

class RendersFromMarkdownTest extends TestCase
{
    public function testRenders()
    {
        $htmlable = new class() {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return '**foo**, _bar_';
            }
        };

        $html = $htmlable->parseMarkdown();

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertStringContainsString('<p><strong>foo</strong>, <em>bar</em></p>', $html);
    }

    public function testRendersEmptyString()
    {
        $htmlable = new class() {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return null;
            }
        };

        $html = $htmlable->parseMarkdown();

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertStringContainsString('', $html);
    }

    public function testRendersMultipleLinesFromArray()
    {
        $htmlable = new class() {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return ['**foo**', '_bar_'];
            }
        };

        $html = $htmlable->parseMarkdown();

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertStringContainsString('<strong>foo</strong>', $html);
        $this->assertStringContainsString('<em>bar</em>', $html);
    }

    public function testRendersMultipleLinesFromCollection()
    {
        $htmlable = new class() {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return collect(['**foo**', '_bar_']);
            }
        };

        $html = $htmlable->parseMarkdown();

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertStringContainsString('<strong>foo</strong>', $html);
        $this->assertStringContainsString('<em>bar</em>', $html);
    }

    public function testRendersAsString()
    {
        $htmlable = new class() {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return '**foo**_bar_';
            }
        };

        $this->assertStringContainsString('<p><strong>foo</strong><em>bar</em></p>', (string)$htmlable);
    }

    public function testRendersAsHtml()
    {
        $htmlable = new class() implements Htmlable {
            use RendersFromMarkdown;

            protected function getMarkdown()
            {
                return '**foo**_bar_';
            }
        };

        $this->assertStringContainsString('<p><strong>foo</strong><em>bar</em></p>', $htmlable->toHtml());
    }
}
