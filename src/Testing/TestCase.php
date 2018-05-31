<?php

namespace Nonetallt\Jinitialize\Testing;

use PHPunit\Framework\TestCase as Test;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\JinitializeCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Nonetallt\Jinitialize\JinitializeContainer;
use Nonetallt\Jinitialize\Testing\Constraints\ContainerContains;
use Nonetallt\Jinitialize\Testing\Constraints\ContainerEquals;
use Nonetallt\Jinitialize\Procedure;

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
    protected function runCommand(string $class, array $args = [], array $input = [])
    {
        if(! is_subclass_of($class, JinitializeCommand::class)) {
            return $this->executeCommand($class, $input);
        }

        $this->app->registerCommands('test', [$class]);

        return $this->executeCommand(new $class('test'), $args, $input);
    }

    protected function runCommandAsProcedure(string $class)
    {
        $commands = $this->app->registerCommands('test', [$class]);
        $procedure = new Procedure('test:procedure', 'This is a test', $commands);
        $this->app->add($procedure);

        return $this->executeCommand($procedure);
    }

    protected function runProcedure(array $commands)
    {
        $commands = $this->app->registerCommands('test', $commands);
        $procedure = new Procedure('test:procedure', 'This is a test', $commands);
        $this->app->add($procedure);

        return $this->executeCommand($procedure);
    }

    private function executeCommand($command, array $args = [], array $input = [])
    {
        $name = $command;

        if(! is_string($command)) {
            $name = $command->getName();
        }

        $command = $this->app->find($name);
        $tester = new CommandTester($command);
        $tester->setInputs($input);
        $tester->execute(array_merge($args, ['command' => $command->getName()]));

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

        if(! $this->app->getContainer()->hasPlugin('test')) {
            $this->app->registerPlugin(['name' => 'test']);
        }
    }

    protected function tearDown()
    {
        /* Clear all values from the singleton container */
        JinitializeContainer::resetInstance();
    }
}
