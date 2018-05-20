<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;
use Nonetallt\Jinitialize\JinitializeApplication;
use Tests\Traits\Paths;
use Tests\Classes\TestApplication;

use Tests\Classes\TestExportCommand;
use Nonetallt\Jinitialize\Procedure;

class ExportImportVariablesTest extends TestCase
{
    use Paths;

    public function testExportVariablesToContainer()
    {
        $app = new TestApplication($this->projectRoot());
        $exportCommand = TestExportCommand::class;
        $container = $app->getContainer();

        $app->testCommands([$exportCommand]);

        var_dump($exportCommand->getPlugin('test')->getContainer());

        $this->assertEquals(['variable1', 'variable2'], $container->getPlugin('test')->getContainer()->getData());
    }

    public function testImportVariablesFromContainer()
    {

    }
}
