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

    public function isFatal()
    {
        foreach($this->errors as $error) {
            if($error->isFatal()) return true;
        }
        return false;
    }

    public function __toString()
    {
        $message = '';

        foreach($this->errors as $error) {
            $message .= (string)$error . PHP_EOL;
        }
        return $message;
    }
}
