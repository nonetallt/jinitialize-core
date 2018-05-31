<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Classes\TestSumCommand;
use Tests\Classes\TestSumArgumentsCommand;

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
}
