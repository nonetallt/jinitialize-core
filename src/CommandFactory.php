<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Exception\CommandNotFoundException as ConsoleException;
use Nonetallt\Jinitialize\Exceptions\CommandNotFoundException;

class CommandFactory
{
    private $app;

    public function __construct(JinitializeApplication $app)
    {
        $this->app = $app;
    }

    public function create(string $plugin, string $name)
    {
        /* echo text, where echo is the name of the command and the string
           after represents the arguments */

        $parts = explode(' ', $name, 2);
        $name      = $parts[0];
        $arguments = $parts[1] ?? '';

        $name = self::commandSignatureFor($plugin, $name);

        try{
            $command = $this->app->getNew($name);
            $command->setName($name);
            $command->setInput(new StringInput($arguments));
            return $command;
        }
        catch(ConsoleException $e) {
            throw new CommandNotFoundException("Command $name was not found");
        }
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
    public static function setNamespace(JinitializeCommand $command)
    {
        /* Check that the given name is in the plugin's namespace */
        $name = $command->getName();
        $plugin = $command->getPluginName();

        $command->setName(self::commandSignatureFor($plugin, $name));
        return $command;
    }

    public static function commandSignatureFor(string $plugin, string $command)
    {
        $parts = explode(':', $command, 2);

        if(count($parts) === 1) {
            /* Append namespace if missing */
            $command = "$plugin:{$parts[0]}";
        }
        else {
            /* Check that namespace is correct */
            if($parts[0] !== $plugin) {
                $command = $plugin .':'. $parts[1];
            }
        }
        return $command;
    }
}
