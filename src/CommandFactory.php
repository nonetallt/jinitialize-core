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

        return $this->setNamespace($command);
    }

    /**
     * Make sure that the namespace before command matches that of the plugin
     * of the command.
     *
     * namespace:command
     *
     * @param JinitializeCommand $command
     * @return JinitializeCommand $command
     *
     */
    private function setNamespace(JinitializeCommand $command)
    {
        /* Check that the given name is in the plugin's namespace */
        $name = $command->getName();
        $plugin = $command->getPluginName();

        $parts = explode(':', $name, 2);

        if(count($parts) === 1) {
            /* Append namespace if missing */
            $name = "$plugin:{$parts[0]}";
        }
        else {
            /* Check that namespace is correct */
            if($parts[0] !== $plugin) {
                $name = $plugin .':'. $parts[1];
            }
        }

        $command->setName($name);
        return $command;
    }
}
