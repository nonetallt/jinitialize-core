<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Tests\Traits\MocksCommands;
use Tests\Traits\Paths;

class ProcedureTest extends TestCase
{
    use MocksCommands, Paths;

    /**
     * Make sure class can be initialized
     */
    function testInitializeClass()
    {
        $commands = [];
        $procedure = new Procedure('test', 'description', $commands);
        $this->assertInstanceOf(Procedure::class, $procedure);
    }

    /**
     * Make sure procedures can execute all the commands
     */
    function testExecuteMultipleCommands()
    {
        $commandTester = new CommandTester($this->createCommand());
        $commandTester->execute([
            'command' => 'test'
        ]);

        /* Assert that both commands write their name in the output file */
        $this->assertEquals("example1example2", file_get_contents($this->outputFile()));
    }

    /**
     * Make sure procedures can revert all the commands
     */
    function testRevertCommands()
    {
        $procedure = $this->createCommand();
        $commandTester = new CommandTester($procedure);
        $commandTester->execute([
            'command' => 'test'
        ]);

        $procedure->revert();

        /* Assert that both commands write their name in the output file */
        $this->assertEquals("", file_get_contents($this->outputFile()));
    }

    /**
     * Cleans the output folder before each test
     */
    public function setUp()
    {
        self::cleanOutput();
    }

    private function createCommand()
    {
        $app = new Application();

        $commands = [
            $this->getMockCommand('1'),
            $this->getMockCommand('2')
        ];

        foreach($commands as $command) {
            $app->add($command);
        }

        $procedure = new Procedure('test', 'description', $commands);

        $app->add($procedure);
        $command = $app->find('test');

        return $command;
    }
}
