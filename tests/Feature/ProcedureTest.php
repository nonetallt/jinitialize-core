<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Tests\Traits\Paths;
use Tests\Traits\CleansOutput;
use Tests\Classes\TestApplication;

class ProcedureTest extends TestCase
{
    use CleansOutput;

    /**
     * Make sure class can be initialized
     */
    public function testClassCanBeInitialized()
    {
        $commands = [];
        $procedure = new Procedure('test', 'description', $commands);
        $this->assertInstanceOf(Procedure::class, $procedure);
    }

    /**
     * Make sure procedures can execute all the commands
     */
    public function testExecuteMultipleCommands()
    {
        $app = new TestApplication($this->projectRoot());
        $command1 = $this->mockCommand($app, 'example1');
        $command2 = $this->mockCommand($app, 'example2');
        $app->executeCommands([$command1, $command2]);

        /* Assert that both commands write their name in the output file */
        $this->assertEquals("example1example2", file_get_contents($this->outputFile()));
    }

    /**
     * Make sure procedures can revert all the commands
     */
    public function testRevertCommands()
    {
        /* $app = new TestApplication($this->projectRoot()); */
        /* $command1 = $this->mockCommand($app, 'example1'); */
        /* $command2 = $this->mockCommand($app, 'example2'); */
        /* $procedure = $app->testProcedure('example', [$command1, $command2]); */
        /* $procedure->revert(); */

        /* /1* Assert that both commands write their name in the output file *1/ */
        /* $this->assertEquals("", file_get_contents($this->outputFile())); */
    }

    public function testCantBeCreatedWithDuplicateInstanceCommands()
    {
        $msg = "A procedure should never be initialized with duplicate command objects (example)";
        $this->expectExceptionMessage($msg);

        $app = new TestApplication($this->projectRoot());
        $command = $this->mockCommand($app, 'example');
        $procedure = new Procedure('test', 'test', [$command, $command]);
    }

    private function mockCommand($app, string $name)
    {
        $command = $app->createCommand($name, function($command) use($name){
            $this->mockHandle($name);
        }, 
        function($command) use ($name){
            $this->mockRevert($name);
        });

        return $command;
    }

    /**
     * Create a callback function for command revert
     */
    private function mockRevert(string $name)
    {
        $file = $this->outputFile();
        $contents = '';

        /* If no file exists, create one */
        if(file_exists($file)) {
            $contents = file_get_contents($file);
        }
        file_put_contents($file, str_replace($name, '', $contents));
    }

    /**
     * Create a callback function for command handle
     */
    private function mockHandle(string $name)
    {
        $file = $this->outputFile();
        file_put_contents($file, $name, FILE_APPEND);
    }
}
