<?php

namespace Nonetallt\Jinitialize\Exceptions;


class PluginNotFoundException extends \Exception
{
    private $command;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
