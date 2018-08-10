<?php

namespace Nonetallt\Jinitialize\JinScript;

class JinScriptError
{
    private $type;
    private $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function isFatal()
    {
        return $this->type === 'fatal';
    }
}
