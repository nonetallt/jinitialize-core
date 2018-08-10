<?php

namespace Nonetallt\Jinitialize\JinScript;

class JinScriptErrors
{
    private $errors;
    
    public function __construct()
    {
        $this->errors = [];
    }

    public function fatal(string $message)
    {
        $this->errors[] = new JinScriptError('fatal', $message);
    }

    public function isEmpty()
    {
        return count($this->errors) === 0;
    }
}
