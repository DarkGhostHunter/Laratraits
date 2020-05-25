<?php

namespace Tests;

use LogicException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Http;
use DarkGhostHunter\Laratraits\SendsToHttp;

class SendsToHttpTest extends TestCase
{
    public function test_sends_to_http()
    {
        Http::fake([
            'go.com' => Http::response([''], 200, ['Headers']),
        ]);

        $class = new class {
            use SendsToHttp;

            public $url = 'go.com';

            protected function toHttp()
            {
                return ['foo' => 'bar'];
            }
        };

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $class->send();

        $this->assertSame(200, $response->status());
    }

    public function test_exception_when_no_url_set()
    {
        $class = new class {
            use SendsToHttp;

            protected function toHttp()
            {
                return ['foo' => 'bar'];
            }
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('This instance of ' . get_class($class) . ' has no default URL to be sent to.');

        $class->send();
    }
}
