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
        $this->setAttribute('variable1', 'value1');
        $this->setAttribute('variable2', 'value2');
    }

    public function exportsVariables()
    {
        return [
            'variable1',
            'variable2'
        ];
    }
}
