<?php

namespace Nonetallt\Jinitialize\Helpers;

use SebastiaanLuca\StubGenerator\StubGenerator;

class Project
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public static function isPathValid(string $path = null)
    {
        if(is_null($path)) $path = $this->basePath;

        /* Check that there is no file/folder with project name and that parent folder is writable */
        return ! file_exists($path) && is_writable(dirname($path));
    }

    public function createFolder(int $access = 0755, bool $recursive)
    {
        return mkdir($this->basePath, $access, $recursive);
    }

    public function createStructure(array $structure)
    {
        $folder = new Folder($this->getFolderName(), $structure);
        $folder->create($this->getParentFolderPath(), 0755, true);
    }

    public function getParentFolderPath()
    {
        return dirname($this->basePath);
    }

    public function getFolderName()
    {
        return Strings::lastAfter($this->basePath, '/');
    }

    public function copyStubsFrom(string $path, array $replacements)
    {
        if(! $this->isPathValid($path)) {
            return false;
        }
        $files = array_diff(scandir($path), ['.', '..']);

        foreach($files as $file) {
            $stub = new StubGenerator("$path/$file", "$this->basePath/$file");
            $test = $stub->render($replacements);
        }    
    }

    public function copyFilesFrom(string $path)
    {
        if(! $this->isPathValid($path)) {
            return false;
        }

        $files = array_diff(scandir($path), ['.', '..']);

        foreach($files as $file) {
            $content = file_get_contents("$path/$file");
            file_put_contents("$this->basePath/$file", $content);
        }
        return true;
    }

    public function getPath()
    {
        return $this->basePath;
    }

}
