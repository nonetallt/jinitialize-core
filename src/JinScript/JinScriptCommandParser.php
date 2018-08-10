<?php

namespace Nonetallt\Jinitialize\JinScript;

use Nonetallt\Jinitialize\CommandFactory;
use Nonetallt\Jinitialize\JinitializeApplication;

class JinScriptCommandParser
{
    private $command;
    private $arguments;
    private $options;

    public function __construct(string $command)
    {
        $this->command = trim($command);
        $this->arguments = null;
        $this->options = null;
    }

    public function createJinitializeCommand(JinitializeApplication $app, string $plugin)
    {
        $factory = new CommandFactory($app);
        $command = $factory->create($plugin, $this->getCommandString());
        return $command;
    }

    private function parse()
    {
        $split = \Clue\Arguments\split($this->command);
        $trackArgs = true;
        $args = [];
        $options = [];

        foreach($split as $param) {
            if($param === 'with') $trackArgs = false;
            else if($param === $this->getName()) continue;
            else if($trackArgs) $args[] = $param;
            else $options[] = $param;
        }

        $this->arguments = $args;
        $this->options = $this->prependSlashesToOptions($options);
    }

    /**
     * Prepend -- before each option
     */
    private function prependSlashesToOptions(array $options)
    {
        foreach($options as $option) {
            $converted[] = "--$option";
        }
        return $converted;
    }

    public function getParameters()
    {
        return array_merge($this->getArguments(), $this->getOptions());
    }

    public function getParameterString()
    {
        return implode(' ', $this->getParameters());
    }

    public function getOptions()
    {
        if(is_null($this->options)) $this->parse();
        return $this->options;
    }

    public function getArguments()
    {
        if(is_null($this->arguments)) $this->parse();
        return $this->arguments;
    }

    public function getName()
    {
        return explode(' ', $this->command)[0];
    }

    public function getCommandString()
    {
        return $this->getName() . ' ' . $this->getParameterString();
    }
}
