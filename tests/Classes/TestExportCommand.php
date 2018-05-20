<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\Plugin\JinitializeCommand;

class TestExportCommand extends JinitializeCommand
{
    protected function configure()
    {
        $this->setName('variables:export');
        $this->setDescription('Export variables test');
    }

    protected function handle()
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
