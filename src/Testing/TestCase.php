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

    public function __construct()
    {
        parent::__construct();

        $this->app = new JinitializeApplication();
        $this->registerLocalPlugin();
    }

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
     * Execute a registered command using the command signature
     * 
     * @param string $commandName
     * @return CommandTester $tester
     *
     */
    protected function executeCommand(string $commandName)
    {
        $command = $this->app->find($commandName);
        return $this->testComamnd($command);
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
            throw new \Exception("Class $class given to runCommand should be a subclass of JinitializeCommand");
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
        return $this->testCommand($command);
    }

    private function testCommand($command)
    {
        $command = $this->app->find($command->getName());
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

    public function tearDown()
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
