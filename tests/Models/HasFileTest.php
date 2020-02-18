<?php

namespace Tests\Models;

use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Models\HasFile;

class HasFileTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filesystems.disks.test', [
            'driver' => 'local',
            'root'   => storage_path('app/test'),
        ]);
    }

    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {

            Schema::create('foo', function (Blueprint $table) {
                $table->increments('id');
                $table->string('disk');
                $table->string('path');
                $table->string('file_hash');
                $table->timestamps();
            });

            Schema::create('bar', function (Blueprint $table) {
                $table->increments('id');
                $table->string('test_disk');
                $table->string('test_path');
                $table->string('test_file_hash');
                $table->timestamps();
            });

            Storage::disk()->delete('test_path.txt');
            Storage::disk('test')->delete('test_path.txt');
        });

        parent::setUp();
    }

    public function testSetsAndSavesFile()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());
        $model->saveFileContents();

        $this->assertStringEqualsFile(storage_path('app/test_path.txt'), $string);
    }

    public function testSavesFile()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->saveFileContents($string = Str::random());

        $this->assertStringEqualsFile(storage_path('app/test_path.txt'), $string);
    }

    public function testSavesFileWhenSavingModel()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $this->assertStringEqualsFile(storage_path('app/test_path.txt'), $string);
    }

    public function testSavesFileWhenSavingModelEvenIfModelHasNoChanges()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $model = TestFileModel::first();

        $model->setFileContents($string = Str::random());

        $model->save();

        $this->assertStringEqualsFile(storage_path('app/test_path.txt'), $string);
    }

    public function testSavesDiskAndPathWhenSavingModel()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $this->assertEquals('local', $model->disk);
        $this->assertEquals('test_path.txt', $model->path);
    }

    public function testCantSaveWithoutPath()
    {
        $this->expectException(\LogicException::class);

        $model = new TestFileModel;
        $model->setFileContents($string = Str::random());

        $model->save();

        $this->assertEquals('local', $model->disk);
        $this->assertEquals('test_path.txt', $model->path);
    }

    public function testRetrievesFile()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $model = TestFileModel::first();

        $this->assertEquals($string, $model->getFileContents());
    }

    public function testUsesCustomDisk()
    {
        $model = new TestFileModel;
        $model->setAttribute('disk', 'test');
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $model = TestFileModel::first();

        $this->assertEquals($string, $model->getFileContents());
    }

    public function testDoesntSavesFileIfNotChanged()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $hash = $model->saveFileContents($string = Str::random());

        $model->save();

        /** @var \Illuminate\Contracts\Filesystem\Filesystem $storage */
        $storage = Storage::fake('local');

        $model = TestFileModel::first();

        $model->setFileContents($string);

        $this->assertTrue($model->saveFileContents());

        $this->assertEmpty($storage->files('/'));
    }

    public function testDoesntSavesWhenSavingModelIfNotChanged()
    {
        $model = new TestFileModel;
        $model->setAttribute('path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        /** @var \Illuminate\Contracts\Filesystem\Filesystem $storage */
        $storage = Storage::fake('local');

        $model = TestFileModel::first();

        $this->assertTrue($model->save());

        $this->assertEmpty($storage->files('/'));
    }

    public function testUsesCustomAttributes()
    {
        $model = new TestCustomFileModel;
        $model->setAttribute('test_path', 'test_path.txt');
        $model->setFileContents($string = Str::random());

        $model->save();

        $model = TestCustomFileModel::first();

        $this->assertEquals($string, $model->getFileContents());
    }

    public function testDetectsFileChanges()
    {
        $model = new TestCustomFileModel;
        $model->setAttribute('test_path', 'test_path.txt');
        $hash = $model->setFileContents($string = Str::random());

        $model->save();

        $model = TestCustomFileModel::first();

        $newHash = $model->setFileContents($string);

        $this->assertEquals($string, $model->getFileContents());
        $this->assertEquals($hash, $newHash);

        $this->assertFalse($model->isFileChanged());
    }

    protected function tearDown() : void
    {
        Storage::disk()->delete('test_path.txt');
        Storage::disk('test')->delete('test_path.txt');

        parent::tearDown();
    }
}

class TestFileModel extends Model
{
    use HasFile;

    protected $table = 'foo';
}

class TestCustomFileModel extends Model
{
    use HasFile;

    protected $table = 'bar';

    protected function getQualifiedFileHashColumn()
    {
        return 'test_file_hash';
    }

    protected function getQualifiedStorageDiskColumn()
    {
        return 'test_disk';
    }

     protected function getQualifiedStoragePathColumn()
     {
         return 'test_path';
     }
}
