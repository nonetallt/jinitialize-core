<?php

namespace Tests\Unit;

use Tests\Classes\TestRequiresExecutionCommand;
use Tests\Classes\TestRecommendsExecutionCommand;
use Tests\Classes\TestExportCommand;

use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Procedure;

class JinitializeCommandTest extends TestCase
{
    
    public function testRecommendsExecutingFail()
    {
        $tester = $this->runCommandAsProcedure(TestRecommendsExecutionCommand::class);
        $output = $tester->getDisplay();
        $this->assertContains('[NOTE] Procedure test:procedure has methods that recommend', $output);
    }

    public function testRecommendsExecutingSuccess()
    {
        $tester = $this->runProcedure([ 
            TestExportCommand::class,
            TestRecommendsExecutionCommand::class
        ]);

        $output = $tester->getDisplay();
        $this->assertNotContains('[NOTE] Procedure test:procedure has methods that recommend', $output);
    }

    public function testRequiresExecutingFail()
    {
        $this->expectException(CommandAbortedException::class);
        $this->runCommandAsProcedure(TestRequiresExecutionCommand::class);
    }

    public function testRequiresExecutingSuccess()
    {
        $commands = [
            TestExportCommand::class,
            TestRequiresExecutionCommand::class
        ];

        $this->runProcedure($commands);
        $this->assertContainerContains(['variable1' => 1]);
    }

    public function testBelongsToProcedureFalse()
    {
        $command = new TestExportCommand('test');
        $this->assertFalse($command->belongsToProcedure());
    }

    public function testBelongsToProcedureTrue()
    {
        $command = new TestExportCommand('test');
        $procedure = new Procedure('test', 'this is a test', [$command]);
        $this->assertTrue($command->belongsToProcedure());
    }
}
