<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\Paths;
use Nonetallt\Jinitialize\ProcedureFactory;
use Nonetallt\Jinitialize\Procedure;

class ProcedureFactoryTest extends TestCase
{
    use Paths;

    private $procedure;

    public function testInitializeClass()
    {
        $this->assertInstanceOf(Procedure::class, $this->procedure);
    }

    public function testHasCommands()
    {
        $commands = [];

        foreach($this->procedure->getCommands() as $command) {
            $commands[] = $command->getName();
        }

        $this->assertEquals(['create:plugin'], $commands);
    }

    public function testCommandsHaveArguments()
    {
        $commands = $this->procedure->getCommands();
        $input = $commands[0]->getInput();
        $this->assertEquals('test', $input->getFirstArgument());
    }

    public function setUp()
    {
        parent::setUp();

        $file = $this->stubsFolder() . '/procedure.json';
        $factory = new ProcedureFactory($this->getApplication(), [$file]);
        $this->procedure = $factory->create('test-procedure');
    }
}
