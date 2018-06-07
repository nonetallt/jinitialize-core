<?php

namespace Nonetallt\Jinitialize\Common\Traits;

use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;

trait AbortsExecution
{
    public function abort(string $message, \Exception $original = null)
    {
        $exception = new CommandAbortedException($message, 0, $original);
        $exception->setCommand($this);
        throw $exception;
    }
}
