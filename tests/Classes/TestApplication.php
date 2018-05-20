<?php

namespace Tests\Classes;

use Symfony\Component\Console\Tester\CommandTester;
use Nonetallt\Jinitialize\Plugin\Plugin;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\Procedure;

class TestApplication extends JinitializeApplication
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function testCommands(array $commands)
    {
        /* Register the commands as a single procedure to the test plugin */
        $this->registerPlugin($this->createPlugin('test', $commands));

        $this->executeCommands($commands);
    }

    private function executeCommands(array $commands)
    {
        foreach($commands as $commandClass) {
            $command = $this->find($command->getName());
            $commandTester = new CommandTester($command);

            $commandTester->execute([
                'command' => $command->getName()
            ]);
        }
    }

    private function createPlugin(string $name, array $commands)
    {
        $plugin = new class($name, $commands) extends Plugin {

            private $commands;

            function __construct(string $name, array $commands)
            {
                parent::__construct($name);
                $this->commands = $commands;
            }

            function commands()
            {
                return $this->commands;

            }

            function procedures()
            {
                return [
                    /* new Procedure('test', 'Run a test procedure', $this->commands) */
                ];
            }
        };
    
        return $plugin;
    }
}
