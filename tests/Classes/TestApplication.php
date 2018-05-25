<?php

namespace Tests\Classes;

use Symfony\Component\Console\Tester\CommandTester;
use Nonetallt\Jinitialize\Plugin\Plugin;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\JinitializeCommand;

class TestApplication extends JinitializeApplication
{
    private $plugin;

    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function createCommand(string $name, callable $handle, callable $revert) {
        $mock =  new class($name, $handle, $revert) extends JinitializeCommand {

            private $name;
            private $desc;
            private $handle;

            public function __construct($name, $handle, $revert)
            {
                $this->name = $name;
                $this->handle = $handle;
                $this->revert = $revert;
                parent::__construct('testPlugin');
            }

            protected function configure()
            {
                $this->setName($this->name);
            }

            protected function handle($input, $ouput, $style) 
            {
                ($this->handle)($this);
            }

            public function revert() 
            {
                ($this->revert)($this);
            }
        };
        $this->add($mock);
        return $mock;
    }

    public function testProcedure(string $name, array $commands)
    {
        $procedure = new Procedure($name, 'desc', $commands);
        $this->add($procedure);
        $this->executeCommands([ $procedure ]);

        return $procedure;
    }

    public function executeCommands(array $commands)
    {
        foreach($commands as $command) {
            $command = $this->find($command->getName());
            $commandTester = new CommandTester($command);

            $commandTester->execute([
                'command' => $command->getName()
            ]);
        }
    }
}
