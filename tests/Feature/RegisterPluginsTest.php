<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Tests\Traits\Paths;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\JinitializeContainer;

class RegisterPluginTest extends TestCase
{
    use Paths;

    private $app;

    public function testPluginsAreRegistered()
    {
        $this->assertTrue($this->app->getContainer()->hasPlugin('TestPlugin'));
    }

    public function testCommandsAreRegistered()
    {
        /* Assert that the export and import commands were registered */
        $this->assertCount(2, $this->app->all('TestPlugin'));
    }

    public function testProceduresAreRegistered()
    {
        $procedure = $this->app->find('test-procedure');
        $commands = $procedure->getCommands();

        /* Make sure the procedure is registered in app */
        $this->assertTrue($this->app->has('test-procedure'));

        /* Make sure the procedure has both commands */
        $this->assertCount(2, $commands);
    }

    public function setUp()
    {
        $manifestPath = $this->stubsFolder() . '/manifest.php';
        $this->app = new JinitializeApplication();
        $this->app->registerPlugins($manifestPath);
    }

    public function tearDown()
    {
        JinitializeContainer::resetInstance();
    }
}
