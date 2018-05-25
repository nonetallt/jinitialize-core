<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

use Tests\Traits\CleansOutput;

use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\JinitializeCommand;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;

class CommandAbortedExceptionTest extends TestCase
{
    use CleansOutput;

    private $exception;

    public function testCausedByGeneric()
    {
        $this->exception->setCommand(new Command());
        $this->assertFalse($this->exception->causedByProcedure());
        $this->assertFalse($this->exception->causedByCommand());
    }

    public function testCausedByProcedure()
    {
        $this->exception->setCommand(new Procedure('name', 'description', []));
        $this->assertTrue($this->exception->causedByProcedure());
    }

    public function testCausedByCommand()
    {
        /* $this->exception->setCommand($this->getMockCommand(1)); */
        /* $this->assertTrue($this->exception->causedByCommand()); */
    }

    
    /**
     * Cleans the output folder before each test
     */
    public function setUp()
    {
        $this->cleanOutput();
        $this->exception = new CommandAbortedException('message');
    }
}
