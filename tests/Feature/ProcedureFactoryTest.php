<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\Paths;
use Nonetallt\Jinitialize\ProcedureFactory;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Exceptions\CommandNotFoundException;
use Nonetallt\Jinitialize\Exceptions\PluginNotFoundException;
use Tests\Classes\TestSumArgumentsCommand;

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

    public function testCreatedProceduresHaveArguments()
    {
        $procedure = $this->createProcedure('test-duplicate-commands');
        $commands = $procedure->getCommands();

        foreach($commands as $command) {
            $this->assertEquals('test', $command->getInput()->getFirstArgument());
        }
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

    public function testGetNamesMethodReturnsListContainingAllFactoryProcedureNames()
    {
        $factory = $this->createFactory();
        $names = [
            'test-procedure',
            'test-missing-command',
            'test-missing-plugin',
            'test-duplicate-commands',
            'env-placeholder',
            'exported-placeholder',
            'env-exported-placeholder',
            'format-placeholder',
        ];
        $this->assertEquals($names, $factory->getNames());
    }

    public function testProceduresCanWorkWithDuplicateCommands()
    {
        $procedure = $this->createProcedure('test-duplicate-commands');
        $this->assertInstanceOf(Procedure::class, $procedure);
    }

    public function testEnvPlaceholdersFromProcedureScriptsAreReplaced()
    {
        $_ENV['NUMBER1'] = 1;
        $_ENV['NUMBER2'] = 2;
        $_ENV['NUMBER3'] = 3;

        $app = $this->getApplication();
        $app->registerCommands('test', [TestSumArgumentsCommand::class]);
        $procedure = $this->createProcedure('env-placeholder');
        $app->add($procedure);
        $this->runProcedure($procedure->getName());

        $this->assertContainerContains([
            'sum' => 6
        ]);
    }

    public function testExportedPlaceholderFromProcedureScriptsAreReplaced()
    {
        $app = $this->getApplication();
        $app->registerCommands('test', [TestSumArgumentsCommand::class]);
        $procedure = $this->createProcedure('exported-placeholder');
        $app->add($procedure);
        $this->runProcedure($procedure->getName());

        $this->assertContainerContains([
            'sum' => 18
        ]);
    }

    public function testExportedAndEnvPlaceholdersFromProcedureScriptsAreReplacedAtTheSameTime()
    {
        $_ENV['NUMBER1'] = 1;
        $_ENV['NUMBER2'] = 2;

        $app = $this->getApplication();
        $app->registerCommands('test', [TestSumArgumentsCommand::class]);
        $procedure = $this->createProcedure('env-exported-placeholder');
        $app->add($procedure);
        $this->runProcedure($procedure->getName());

        $this->assertContainerContains([
            'sum' => 9
        ]);
    }

    public function testPlaceholdersAreParsedCorrectlyWhenUsingDifferentFormat()
    {
        $_ENV['JINITIALIZE_PLACEHOLDER_FORMAT'] = '{{$}}';
        $_ENV['NUMBER1'] = 1;
        $_ENV['NUMBER2'] = 2;

        $app = $this->getApplication();
        $app->registerCommands('test', [TestSumArgumentsCommand::class]);
        $procedure = $this->createProcedure('format-placeholder');
        $app->add($procedure);
        $tester = $this->runProcedure($procedure->getName());

        $this->assertContainerContains([
            'sum' => 6
        ]);
    }

    private function createFactory()
    {
        return new ProcedureFactory($this->getApplication(), [
            $this->stubsFolder() . '/procedure.json',
            $this->inputFolder('placeholder-variable-procedures.json')
        ]);
    }

    private function createProcedure(string $procedure)
    {
        $factory = $this->createFactory();
        return $factory->create($procedure);
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__ . '/../../composer.json');
        $this->procedure = $this->createProcedure('test-procedure');
    }
}
