<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

use Symfony\Component\Console\Input\InputArgument;

class TestRevertCommand extends JinitializeCommand
{
    private $input;
        
    protected function configure()
    {
        $this->setName('test:revert');
        $this->setDescription('Test revert command');
        $this->addArgument('input', InputArgument::REQUIRED, 'input');
    }

    protected function handle($input, $output, $style)
    {
        $this->input = $input->getArgument('input');
    }

    public function revert()
    {
        return $this->input;
    }
}
