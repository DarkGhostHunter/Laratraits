<?php

namespace Tests\Blade;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use DarkGhostHunter\Laratraits\Blade\RegistersFileDirective;
use const DIRECTORY_SEPARATOR as DS;

class RegistersFileDirectiveTest extends TestCase
{
    public function test_registers_directive()
    {
        Blade::directive('foo', [TestBladeDirective::class, 'register']);

        $foo = Blade::compileString("foo @foo('FOO') bar");
        $bar = Blade::compileString("foo @foo('BAR') bar");
        $quz = Blade::compileString("foo @foo(\$quz) bar");

        $this->assertSame("foo <?php

return strtolower('FOO');
 ?> bar", $foo);
        $this->assertSame("foo <?php

return strtolower('BAR');
 ?> bar", $bar);
        $this->assertSame("foo <?php

return strtolower(\$quz);
 ?> bar", $quz);
    }

    public function test_register_multiple_files()
    {
        Blade::directive('foo', [TestBladeDirectiveMultipleFiles::class, 'foo']);
        Blade::directive('bar', [TestBladeDirectiveMultipleFiles::class, 'bar']);

        $foo = Blade::compileString("foo @foo('FOO') bar");
        $bar = Blade::compileString("foo @bar('BAR') bar");

        $this->assertSame("foo <?php

return strtolower('FOO'); ?> bar", $foo);
        $this->assertSame("foo <?php

return strtolower('BAR'); ?> bar", $bar);
    }

    public function test_exception_when_file_doesnt_exists()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('The class Tests\Blade\TestBladeDirectiveMissing has no file [test_blade_directive_missing.php] to register as directive');

        Blade::directive('foo', [TestBladeDirectiveMissing::class, 'register']);
        Blade::compileString("foo @foo('FOO') bar");
    }

    public function test_receives_directive_without_expression()
    {
        Blade::directive('quz', [TestEmptyDirective::class, 'quz']);

        $foo = Blade::compileString("foo @quz() bar");

        $this->assertSame("foo <?php

return 'sample'; ?> bar", $foo);
    }
}

class TestBladeDirective
{
    use RegistersFileDirective;
}

class TestBladeDirectiveMultipleFiles
{
    use RegistersFileDirective;

    public static function foo($foo)
    {
        return static::register($foo, __DIR__ . DS . 'foo_directive.php');
    }

    public static function bar($bar)
    {
        return static::register($bar, __DIR__ . DS . 'bar_directive.php');
    }
}

class TestBladeDirectiveMissing
{
    use RegistersFileDirective;
}

class TestEmptyDirective
{
    use RegistersFileDirective;

    public static function quz()
    {
        return static::register(null, __DIR__ . DS . 'quz_directive.php');
    }
}
