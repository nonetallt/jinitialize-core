<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Classes\TestSumCommand;
use Tests\Classes\TestSumArgumentsCommand;
use Tests\Classes\TestExportCommand;
use Tests\Classes\TestImportCommand;

class TestCaseTest extends TestCase
{
    
    /**
     * Make sure that user input is testable for runCommand
     */
    public function testRunCommandInput()
    {
        $this->runCommand(TestSumCommand::class, [], [2, 3]);
        $this->assertContainerContains(['sum' => 5]);
    }

    /**
     * Make sure arguments are testable for runCommand
     */
    public function testRunCommandArguments()
    {
        $this->runCommand(TestSumArgumentsCommand::class, ['number1' => 2, 'number2' => 3]);
        $this->assertContainerContains(['sum' => 5]);
    }

    /**
     * Make sure options are testable for runCommand
     */
    public function testRunCommandArgumentsAndOptions()
    {
        $this->runCommand(TestSumArgumentsCommand::class, ['number1' => 2, 'number2' => 3, '--number3' => 5]);
        $this->assertContainerContains(['sum' => 10]);
    }

    /**
     * Make sure there are no problems calling runCommand() multiple times
     */
    public function testRunMultipleCommands()
    {
        $this->runCommand(TestSumCommand::class, [], [1, 1]);
        $this->assertContainerEquals(['sum' => 2]);

        $this->runCommand(TestSumCommand::class, [], [2, 2]);
        $this->assertContainerEquals(['sum' => 4]);
    }

    /**
     * Make sure there are no problems calling runCommandsAsProcedure() multiple times
     */
    public function testRunMultipleProcedures()
    {
        $this->runCommandsAsProcedure([TestExportCommand::class, TestImportCommand::class]);
        $this->assertContainerEquals(['variable1' => 1, 'variable2' => 2, 'variable3' => '12']);

        $this->runCommandsAsProcedure([TestExportCommand::class, TestImportCommand::class]);
        $this->assertContainerEquals(['variable1' => 1, 'variable2' => 2, 'variable3' => '12']);
    }
}
