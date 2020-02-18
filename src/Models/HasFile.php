<?php
/**
 * Has File
 *
 * This trait makes easy to associate a single file to a given model, by saving the location of the file in
 * the model itself in two columns: the disk used by the application (like AWS, Google Drive or whatever)
 * and the path to it. The rest of the information is handled with default methods and the file itself.
 *
 * For this to work, you need to add 3 columns: the "disk" string, the "path" string and the "file_hash"
 * column that will allow to quickly detect changes and save the file if needed.
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

namespace DarkGhostHunter\Laratraits\Models;

use Illuminate\Support\Facades\Storage;

trait HasFile
{
    /**
     * The File Contents.
     *
     * This gets populated when needed.
     *
     * @var string
     */
    protected $file_contents;

    /**
     * Boot the current trait.
     *
     * @return void
     */
    protected static function bootHasFile()
    {
        // This listener will persist the file automatically after saving the model to the database.
        static::saving(function ($model) {
            $model->saveFileContents();
        });
    }

    /**
     * Returns the column that holds the hash of the file.
     *
     * @return string
     */
    protected function getQualifiedFileHashColumn()
    {
        return 'file_hash';
    }

    /**
     * Creates a hash from the file contents for comparison.
     *
     * @param  string  $value
     * @return string
     */
    protected function createFileHash(string $value)
    {
        return sha1($value);
    }

    /**
     * Saves the File contents to the storage disk.
     *
     * @param  string|null  $content
     * @param  string|null  $path
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function saveFileContents(string $content = null, string $path = null)
    {
        if ($content) {
            $this->setFileContents($content);
        }

        if (! $this->file_contents) {
            return false;
        }

        $hash = $this->createFileHash($this->file_contents);

        $hashColumn = $this->getQualifiedFileHashColumn();

        // We will first check if the file has changed in someway. When there is no changes, we will
        // just return true since there is no need to save again the same content again. Otherwise
        // we will save it and also persist the hash of the file to compare if it changed later.
        if (! $this->isFileChanged($hash)) {
            return true;
        }

        if (! $path) {
            $path = $this->getFilePath();
        } else {
            $this->setAttribute($this->getQualifiedStoragePathColumn(), $path);
        }

        if ($this->defaultStorage()->put($path, $this->file_contents)) {
            $this->setAttribute($hashColumn, $hash);
            return true;
        }

        return false;
    }

    /**
     * Returns the File Contents property.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getFileContents()
    {
        if ($this->file_contents) {
            return $this->file_contents;
        }

        return $this->file_contents = $this->defaultStorage()->get($this->getFilePath());
    }

    /**
     * Returns if the file has Changed. Optionally, compared with a given hash.
     *
     * @param  string|null  $hash
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function isFileChanged(string $hash = null)
    {
        $hash = $hash ?? $this->createFileHash($this->getFileContents());

        return $hash !== $this->getAttribute($this->getQualifiedFileHashColumn());
    }

    /**
     * Sets the File content property, and returns the hash of the content.
     *
     * @param  string  $value
     * @return string
     */
    public function setFileContents(string $value)
    {
        return $this->createFileHash($this->file_contents = $value);
    }

    /**
     * The Storage disk to use. If its not set, the default will be used.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function defaultStorage()
    {
        $name = $this->getQualifiedStorageDiskColumn();

        if (! $disk = $this->getAttribute($name)) {
            $this->setAttribute($name, $disk = config('filesystems.default'));
        }

        return Storage::disk($disk);
    }

    /**
     * Returns the column that holds the disk to use
     *
     * @return null|string
     */
    protected function getQualifiedStorageDiskColumn()
    {
        return 'disk';
    }

    /**
     * The path name to use to locate the file in the disk.
     *
     * @return string
     */
    protected function getQualifiedStoragePathColumn()
    {
        return 'path';
    }

    /**
     * Returns the File path.
     *
     * @return string
     */
    protected function getFilePath()
    {
        if ($path = $this->getAttribute($this->getQualifiedStoragePathColumn())) {
            return $path;
        }

        throw new \LogicException('There is no file path set to get/put the file contents.');
    }
}
