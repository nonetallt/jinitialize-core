<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

class TestExportCommand extends JinitializeCommand
{
    protected function configure()
    {
        $this->setName('test:export');
        $this->setDescription('Export variables test');
    }

    protected function handle($input, $output, $style)
    {
        $this->export('variable1', 1);
        $this->export('variable2', 2);
    }

    public function exportsVariables()
    {
        return [
            'variable1',
            'variable2'
        ];
    }
}
