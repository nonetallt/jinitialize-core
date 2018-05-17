<?php

namespace Nonetallt\Jinitialize\Plugin;

class ProcedureFactory
{
    private $paths;

    public function __construct(array $paths)
    {
        /* Filepaths for .json files */
        $this->paths = $paths;
    }

    public function create(string $procedure)
    {
        /* Name of procedure from the .json files */
    }
}
