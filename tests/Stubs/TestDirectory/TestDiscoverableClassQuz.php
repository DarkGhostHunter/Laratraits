<?php

namespace Tests\Stubs\TestDirectory;

use Tests\Stubs\TestInterface;

class TestDiscoverableClassQuz implements TestInterface
{
    public function quz()
    {
        return 'quz';
    }

    public function foo()
    {
        return 'foo';
    }
}
