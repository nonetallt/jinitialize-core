<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TestSumArgumentsCommand extends JinitializeCommand
{
        
    protected function configure()
    {
        $this->setName('test:arguments');
        $this->setDescription('Test input from CommandTester');
        $this->addArgument('number1', InputArgument::REQUIRED, 'the first number');
        $this->addArgument('number2', InputArgument::REQUIRED, 'the second number');
        $this->addOption('number3', null,  InputOption::VALUE_REQUIRED, 'the third number');
    }

    protected function handle($input, $output, $style)
    {
        $number1 = $input->getArgument('number1');
        $number2 = $input->getArgument('number2');
        $number3 = $input->getOption('number3');

        $this->export('sum', $number1 + $number2 + $number3);
    }
}
