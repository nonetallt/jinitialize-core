<?php

namespace Nonetallt\Jinitialize\JinScript;

use Nonetallt\Jinitialize\JinitializeApplication;

class JinScriptPluginParser
{
    private $name;
    private $commands;
    private $app;

    public function __construct(JinitializeApplication $app, string $name)
    {
        $this->name = $name;
        $this->commands = [];
        $this->app = $app;
    }

    /* Check that this plugin is installed */
    public function isInstalled()
    {
        return $this->app->getContainer()->hasPlugin($this->name);
    }

    public function parseCommand(string $line)
    {
        /* Do not try parsing commands if this plugin is not installed */
        if(! $this->isInstalled()) return;

        $parser = new JinScriptCommandParser($line);
        $this->commands[] = $parser->createJinitializeCommand($this->app, $this->name);
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
