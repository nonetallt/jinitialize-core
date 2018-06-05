<?php

namespace Tests\Feature;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\ComposerScripts;
use Tests\Classes\TestImportCommand;
use Tests\Classes\TestExportCommand;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\JinitializeContainer;
use Tests\Classes\TestSumArgumentsCommand;


class JinitializeApplicationTest extends TestCase
{
    use CleansOutput;

    public function testRegisterPlugins()
    {
        $this->cleanOutput();

        /* Create plugins.php file as manifest output */
        $output = $this->outputFolder() . '/plugins.php';

        $data = [[
            'extra' => [
                'jinitialize-plugin' => [
                    'name' => 'test',
                    'commands' => [
                        TestImportCommand::class,
                        TestExportCommand::class,
                    ]
                ]
            ]
        ]];

        ComposerScripts::generatePluginsManifest($data, $output);

        $app = new JinitializeApplication($this->projectRoot());
        $app->registerPlugins($output);

        /* Assert that 2 comamnds from test namespace were registered */
        $this->assertCount(2, $app->all('test'));

        /* Assert that plugin is defined in container */
        $this->assertNotNull(JinitializeContainer::getInstance()->getPlugin('test'));
    }

    public function testRegisteredProceduresHaveArgumentsAndOptions()
    {
        parent::setUp();
        $this->cleanOutput();

        $app = $this->getApplication();
        $app->registerCommands('test', [ TestSumArgumentsCommand::class ]);
        $app->registerProcedures('test', ['procedure.json'], $this->inputFolder());

        $this->runProcedure('test');
        $this->assertContainerEquals(['test' => ['sum' => 6]]);
    }

    public function setUp()
    {
    }
}
