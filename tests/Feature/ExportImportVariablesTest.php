<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;
use Nonetallt\Jinitialize\JinitializeApplication;
use Tests\Traits\Paths;
use Tests\Classes\TestApplication;

use Tests\Classes\TestExportCommand;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\JinitializeContainer;
use Nonetallt\Jinitialize\Plugin;
use Tests\Classes\TestImportCommand;

class ExportImportVariablesTest extends TestCase
{
    use Paths;

    public function testExportVariablesToContainer()
    {
        $app = new TestApplication($this->projectRoot());

        /* Create a new export command in plugin called testPlugin */
        $exportCommand = new TestExportCommand('testPlugin');

        /* Create the mock plugin */
        JinitializeContainer::getInstance()->addPlugin('testPlugin');

        /* Register the command in kernel */
        $app->add($exportCommand);

        /* Execute the command with comand tester */
        $app->executeCommands([$exportCommand]);

        $pluginContainer = JinitializeContainer::getInstance()->getPlugin('testPlugin')->getContainer();
        $this->assertEquals(['variable1' => 1, 'variable2' => 2], $pluginContainer->getData());
    }

    public function testImportVariablesFromContainer()
    {
        $app = new TestApplication($this->projectRoot());

        /* Create a new export command in plugin called testPlugin */
        $exportCommand = new TestExportCommand('testPlugin');
        $importCommand = new TestImportCommand('testPlugin');

        /* Create the mock plugin */
        JinitializeContainer::getInstance()->addPlugin('testPlugin');

        /* Register the command in kernel */
        $app->add($exportCommand);
        $app->add($importCommand);

        /* Execute the command with comand tester */
        $app->executeCommands([$exportCommand, $importCommand]);


        $pluginContainer = JinitializeContainer::getInstance()->getPlugin('testPlugin')->getContainer();
        $this->assertContains('12', $pluginContainer->getData());
    }
}
