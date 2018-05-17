<?php

namespace Nonetallt\Jinitialize\Helpers;

class Project
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function isPathValid(string $path = null)
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
        foreach($structure as $folder => $subfolders) {

            if(empty($subfolders)) {
                mkdir("$to/$folder");
            }
            foreach($subfolders as $subfolder) {
                mkdir("$to/$folder/$subfolder", 0755, true);
            }
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
