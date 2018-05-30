<?php

namespace Tests\Unit;

use Tests\Classes\TestRequiresExecutionCommand;
use Tests\Classes\TestRecommendsExecutionCommand;
use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Tests\Classes\TestExportCommand;

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
}
