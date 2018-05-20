<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;
use Nonetallt\Jinitialize\JinitializeApplication;
use Tests\Traits\Paths;
use Tests\Classes\TestApplication;

use Tests\Classes\TestExportCommand;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Plugin\JinitializeContainer;
use Nonetallt\Jinitialize\Plugin\Plugin;

class ExportImportVariablesTest extends TestCase
{
    use Paths;

    public function testExportVariablesToContainer()
    {
        $app = new TestApplication($this->projectRoot());

        /* Create a new export command in plugin called testPlugin */
        $exportCommand = new TestExportCommand('testPlugin');
        JinitializeContainer::getInstance()->addPlugin(new Plugin('testPlugin'));
        $app->add($exportCommand);
        $app->executeCommands([$exportCommand]);

        $pluginContainer = JinitializeContainer::getInstance()->getPlugin('testPlugin')->getContainer();

        $this->assertEquals(['variable1' => 1, 'variable2' => 2], $pluginContainer->getData());
    }

    public function testImportVariablesFromContainer()
    {

    }
}
