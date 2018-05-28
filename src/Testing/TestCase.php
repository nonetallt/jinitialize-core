<?php

namespace Nonetallt\Jinitialize\Testing;

use PHPunit\Framework\TestCase as Test;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Nonetallt\Jinitialize\JinitializeContainer;
use Nonetallt\Jinitialize\Testing\Constraints\ContainerContains;
use Nonetallt\Jinitialize\Testing\Constraints\ContainerEquals;

class TestCase extends Test
{
    private $app;

    /* Register this plugin for testing purposes in plugins */
    private function registerLocalPlugin()
    {

        $path = __DIR__ . '/../../composer.json';
        $composer = [];

        if(file_exists($path)) {
            $composer = json_decode(file_get_contents($path), true);
        }

        /* Skip packages that do not define plugin in extra */
        if(! empty($composer['extra']['jinitialize-plugin'])) {

            $plugin = $composer['extra']['jinitialize-plugin'];

            /* Avoid multiple registrations */ 
            if(! JinitializeContainer::getInstance()->hasPlugin($plugin['name'])) {
                $this->app->registerPlugin($plugin);
            }
        }
    }
    
    /**
     * Execute a command using the classname
     * 
     * @param string $class
     * @return ComamndTester $tester
     *
     */
    protected function runCommand(string $class)
    {
        if(! is_subclass_of($class, JinitializeCommand::class)) {
            return $this->executeCommand($class);
            /* throw new \Exception("Class $class given to runCommand should be a subclass of JinitializeCommand"); */
        }

        $container = JinitializeContainer::getInstance();

        if(! $container->hasPlugin('test')) {

            $this->app->registerPlugin([
                'name' => 'test',
                'commands' => [
                    $class
                ]
            ]);
        }
        else {
            /* If plugin is already registered, register commands only */
            $this->app->registerCommands('test', [$class]);
        }

        
        $command = new $class('test');
        return $this->executeCommand($command);
    }

    private function executeCommand($command)
    {
        $name = $command;

        if(! is_string($command)) {
            $name = $command->getName();
        }

        $command = $this->app->find($name);
        $tester = new CommandTester($command);
        $tester->execute(['command' => $command->getName()]);

        return $tester;
    }

    protected function assertContainerEquals(array $value, string $plugin = null, $message = '')
    {
        $constraint = new ContainerEquals();
        $constraint->plugin = $plugin;
        self::assertThat($value, $constraint, $message);
    }

    protected function assertContainerContains(array $value, string $plugin = null, $message = '')
    {
        $constraint = new ContainerContains();
        $constraint->plugin = $plugin;
        self::assertThat($value, $constraint, $message);
    }

    protected function getApplication()
    {
        return $this->app;
    }

    protected function setUp()
    {
        $this->app = new JinitializeApplication();
        $this->registerLocalPlugin();
    }

    protected function tearDown()
    {
        /* Clear all values from the singleton container */
        JinitializeContainer::resetInstance();
    }

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }
}
