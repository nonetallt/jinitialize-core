<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Commands\ShellCommand;
use Symfony\Component\Console\Input\StringInput;

class InputValidatorTest extends TestCase
{
    public function setUp()
    {
    }

    public function testRequiredArgumentsReturnsListOfCorrectNames()
    {
        $command = new ShellCommand('test');
        $validator = $command->setInput(new StringInput(''));
        $this->assertEquals(['shell_command'], $validator->requiredArgumentNames());
    }

    public function testMissingArgumentsReturnsNothingWhenAllArgumentsAreProvided()
    {
        $input = new StringInput("'echo test'");
        $command = new ShellCommand('test');
        $validator = $command->setInput($input);

        $this->assertEquals([], $validator->missingArguments($input));
    }

    public function testMissingArguemntsReturnsListOfMissingArguments()
    {
        $input = new StringInput("");
        $command = new ShellCommand('test');
        $validator = $command->setInput($input);

        $this->assertEquals(['shell_command'], $validator->missingArguments($input));
    }
}
