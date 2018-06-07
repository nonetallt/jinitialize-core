<?php

namespace Tests\Traits;

trait Paths
{
    private function projectRoot()
    {
        /* 2 folders up from tests/traits */
        return dirname(dirname(__DIR__));
    }

    private function stubsFolder()
    {
        return $this->projectRoot() . '/stubs';
    }

    private function outputFolder()
    {
        return __DIR__ . '/../output';
    }

    private function inputFolder(string $file = null)
    {
        $path = __DIR__ . '/../input';

        if(! is_null($file)) return "$path/$file";

        return $path;
    }

    private function outputFile()
    {
        return $this->outputFolder() . '/command.out';
    }
}
