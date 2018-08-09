<?php

namespace Nonetallt\Jinitialize\Procedure;

class JinScriptPluginParser
{
    private $name;
    private $commands;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->commands = [];
    }

    public function parseCommand(string $line)
    {
        $this->commands[] = trim($line);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCommands()
    {
        return $this->commands;
    }
}
