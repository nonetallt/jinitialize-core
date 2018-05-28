<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Input\StringInput;

class CommandFactory
{
    private $app;

    public function __construct(JinitializeApplication $app)
    {
        $this->app = $app;
    }

    public function create(string $name)
    {
        /* echo text, where echo is the name of the command and the string
           after represents the arguments */

        $parts = explode(' ', $name, 2);
        $name      = $parts[0];
        $arguments = $parts[1] ?? '';

        $command = $this->app->find($name);
        $command->setInput(new StringInput($arguments));

        return $command;
    }
}
