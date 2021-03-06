<?php

namespace Tests\Unit;

use Tests\Classes\TestRequiresExecutionCommand;
use Tests\Classes\TestRecommendsExecutionCommand;
use Tests\Classes\TestExportCommand;
use Tests\Classes\TestWasOptionPassedCommand;

use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Procedure;

class JinitializeCommandTest extends TestCase
{
    /* TODO move to procedure test */
    public function testRecommendsExecutingFail()
    {
        $tester = $this->runCommandsAsProcedure([ TestRecommendsExecutionCommand::class ]);
        $output = $tester->getDisplay();
        $this->assertContains('[NOTE] Procedure test:procedure has commands that recommend', $output);
    }

    /* TODO move to procedure test */
    public function testRecommendsExecutingSuccess()
    {
        $tester = $this->runCommandsAsProcedure([ 
            TestExportCommand::class,
            TestRecommendsExecutionCommand::class
        ]);

        $output = $tester->getDisplay();
        $this->assertNotContains('[NOTE] Procedure test:procedure has commands that recommend', $output);
    }

    public function testRequiresExecutingFail()
    {
        $this->expectException(CommandAbortedException::class);
        $this->runCommandsAsProcedure([ TestRequiresExecutionCommand::class ]);
    }

    public function testRequiresExecutingSuccess()
    {
        $this->runCommandsAsProcedure([
            TestExportCommand::class,
            TestRequiresExecutionCommand::class
        ]);

        $this->assertContainerContains(['variable1' => 1]);
    }

    public function testIsNotSetExecutedByDefault()
    {
        $command = new TestExportCommand('test');
        $this->assertFalse($command->isExecuted());
    }

    public function testIsSetExecutedAfterExecution()
    {
        $app = $this->getApplication();
        $app->registerCommands('test', [new TestExportCommand('test')]);

        $this->runCommand('test:export');

        $command = $app->find('test:export');
        $this->assertTrue($command->isExecuted());
    }

    public function testWasOptionPassedShouldReturnFalseWhenOptionsAreNotSet()
    {
        $app = $this->getApplication();
        $app->registerCommands('test', [TestWasOptionPassedCommand::class]);
        $this->runCommand('test:option-passed');

        $this->assertContainerEquals(['test' => ['option1' => false, 'option2' => false]]);
    }

    public function testWasOptionPassedShouldReturnTrueWhenOptionsAreSet()
    {
        $app = $this->getApplication();
        $app->registerCommands('test', [TestWasOptionPassedCommand::class]);
        $this->runCommand('test:option-passed --option1 --option2');

        $this->assertContainerEquals(['test' => ['option1' => true, 'option2' => true]]);
    }

    public function testWasOptionPassedShouldReturnTrueWhenOptionShortcutsAreUsed()
    {
        $app = $this->getApplication();
        $app->registerCommands('test', [TestWasOptionPassedCommand::class]);
        $this->runCommand('test:option-passed -a -b');

        $this->assertContainerEquals(['test' => ['option1' => true, 'option2' => true]]);
    }
}
