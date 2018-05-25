<?php

namespace Nonetallt\Jinitialize\Plugin\Exceptions;

use Symfony\Component\Console\Command\Command;
use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Nonetallt\Jinitialize\Procedure;

class CommandAbortedException extends \Exception
{
    private $command;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setCommand(Command $command) 
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function causedByProcedure()
    {
        return is_a($this->command, Procedure::class);
    }

    public function causedByCommand()
    {
        return is_a($this->command, JinitializeCommand::class);
    }
}
