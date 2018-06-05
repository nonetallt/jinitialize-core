<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

use Symfony\Component\Console\Input\InputOption;

class TestWasOptionPassedCommand extends JinitializeCommand
{
        
    protected function configure()
    {
        $this->setName('test:option-passed');
        $this->setDescription('Test that wasOption passed method works');

        $this->addOption('option1', 'a', InputOption::VALUE_NONE, 'Option one');
        $this->addOption('option2', 'b', InputOption::VALUE_NONE, 'Option two');
    }

    protected function handle($input, $output, $style)
    {
        $this->export('option1', $this->wasOptionPassed($input, 'option1'));
        $this->export('option2', $this->wasOptionPassed($input, 'option2'));
    }
}
