<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

class TestRecommendsExecutionCommand extends JinitializeCommand
{
    protected function configure()
    {
        $this->setName('test:recommend-execution');
        $this->setDescription('Recommend execution test');
    }

    protected function handle($input, $output, $style)
    {
    }

    public function recommendsExecuting()
    {
        return [
            TestExportCommand::class
        ];
    }
}
