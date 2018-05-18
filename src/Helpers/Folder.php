<?php

namespace Nonetallt\Jinitialize\Helpers;

class Folder
{
    private $name;
    private $subfolders;

    public function __construct(string $name, array $subfolders)
    {
        $this->name = $name;
        $this->subfolders = self::arrayToFolders($subfolders);
    }

    public static function arrayToFolders(array $folders)
    {
        $output = [];
        foreach($folders as $folder => $subfolders) {
            
            /* If array key is not used as a name, this folder has no subfolders */
            if(is_int($folder)) {
                $output[] = new self($subfolders, []);
            }
            else
            {
                $output[] = new self($folder, $subfolders);
            }
        }
        return $output;
    }

    public function getPath(string $path)
    {
        $folderName = Strings::cutAfter($path, '/');
        $path = Strings::cutUntil($path, '/', false);

        if($this->name === $folderName) {
            return $this;
        }

        $subfolder = $this->getSubfolder($folderName);

        if(is_null($subfolder)) {
            throw new \Exception("Cannot find subfolder $subfolder at path $path");
        }

        return $subfolder->getPath($path);
    }

    public function getSubfolder(string $name)
    {
        foreach($this->subfolders as $subfolder) {
            if($subfolder->getName() === $name) {
                return $subfolder;
            }
        }
        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSubfolders()
    {
        return $this->subfolders;
    }

    public function create(string $path, int $permissions = 0755, bool $recursive = false)
    {
        $mainFolder = "$path/$this->name";

        if(! Project::isPathValid($mainFolder)) {
            throw new \Exception("Cannot create folder structure at $path, path does not exist or is not writable.");
        }

        mkdir($mainFolder, $permissions);

        /* Create folders for all subfolders recursively */
        if($recursive) {
            foreach($this->subfolders as $folder) {
                $folder->create($mainFolder, $permissions, $recursive);
            }
        }
    }

    public function __toString()
    {
        return $this->print();
    }

    /* Recursively return the folder structure */
    public function print(int $level = 1)
    {
        $str = $this->name . PHP_EOL;

        foreach($this->subfolders as $folder) {
            $str .= str_repeat('--', $level);
            $str .= $folder->print($level +1);
        }
        return $str;
    }
}
