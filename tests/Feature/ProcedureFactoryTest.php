<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\Paths;
use Nonetallt\Jinitialize\ProcedureFactory;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Exceptions\CommandNotFoundException;
use Nonetallt\Jinitialize\Exceptions\PluginNotFoundException;

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

        $this->assertEquals(['core:create-plugin'], $commands);
    }

    public function testCommandsHaveArguments()
    {
        $commands = $this->procedure->getCommands();
        $input = $commands[0]->getInput();
        $this->assertEquals('test', $input->getFirstArgument());
    }

    public function testNonexistentCommand()
    {
        $this->expectException(CommandNotFoundException::class);
        $procedure = $this->createProcedure('test-missing-command');
    }

    public function testNonexistentPlugin()
    {
        $this->expectException(PluginNotFoundException::class);
        $procedure = $this->createProcedure('test-missing-plugin');
    }

    public function testGetNames()
    {
        $factory = $this->createFactory();
        $names = ['test-procedure', 'test-missing-command', 'test-missing-plugin'];
        $this->assertEquals($names, $factory->getNames());
    }

    private function createFactory()
    {
        $file = $this->stubsFolder() . '/procedure.json';
        return new ProcedureFactory($this->getApplication(), [$file]);
    }

    private function createProcedure(string $procedure)
    {
        $factory = $this->createFactory();
        return $factory->create($procedure);
    }

    public function setUp()
    {
        parent::setUp();
        $this->procedure = $this->createProcedure('test-procedure');
    }
}
