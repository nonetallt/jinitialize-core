<?php

namespace Nonetallt\Jinitialize\Testing;

use Symfony\Component\Console\Tester\CommandTester as Tester;
use Symfony\Component\Console\Command\Command;

class CommandTester extends Tester
{
    private $command;

    public function __construct(Command $command)
    {
        parent::__construct($command);
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
