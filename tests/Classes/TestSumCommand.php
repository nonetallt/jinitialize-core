<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

class TestSumCommand extends JinitializeCommand
{
        
    protected function configure()
    {
        $this->setName('test:input');
        $this->setDescription('Test input from CommandTester');
    }

    protected function handle($input, $output, $style)
    {
        $number1 = $style->ask('Give first number');
        $number2 = $style->ask('Give second number');

        $this->export('sum', $number1 + $number2);
    }
}
