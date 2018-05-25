<?php

namespace Tests\Classes;

use Nonetallt\Jinitialize\JinitializeCommand;

class TestImportCommand extends JinitializeCommand
{
    protected function configure()
    {
        $this->setName('variables:import');
        $this->setDescription('Import variables test');
    }

    protected function handle($input, $output, $style)
    {
        $v1 = $this->import('testPlugin', 'variable1');
        $v2 = $this->import('testPlugin', 'variable2');
        $this->export('variable3', $v1 . $v2);
    }

    public function exportsVariables()
    {
        return [
            'variable1',
            'variable2'
        ];
    }
}
