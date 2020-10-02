<?php

namespace Nonetallt\Jinitialize\JinScript;

class JinScriptErrors
{
    private $context;
    private $errors;
    
    public function __construct(string $displayContext = '')
    {
        $this->context = $displayContext;
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

    public function setContext(string $value)
    {
        $this->context = $value;
    }

    public function __toString()
    {
        $message = '';
        if($this->context !== '') $message .= PHP_EOL . "[$this->context]" . PHP_EOL;

        foreach($this->errors as $error) {
            $message .= (string)$error . PHP_EOL;
        }
        return $message;
    }
}
