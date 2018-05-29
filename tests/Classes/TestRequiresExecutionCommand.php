<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

class TestRequiresExecutionCommand extends JinitializeCommand
{
    protected function configure()
    {
        $this->setName('test:require-execution');
        $this->setDescription('Require execution test');
    }

    protected function handle($input, $output, $style)
    {
    }

    public function requiresExecuting()
    {
        return [
            TestExportCommand::class
        ];
    }
}
