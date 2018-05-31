<?php

namespace Nonetallt\Jinitialize\Testing;

use PHPunit\Framework\TestCase as Test;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;

use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\JinitializeCommand;
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
     * Execute a command using the classname or command signature
     * 
     * @param string $command
     * @param array $args The command arguments and options
     * @param array $input The user input
     *
     * @return Symfony\Component\Console\Tester\CommandTester $tester
     *
     */
    protected function runCommand(string $command, array $args = [], array $input = [])
    {
        return $this->executeCommand($this->getCommand($command), $args, $input);
    }

    protected function runCommandsAsProcedure(array $commands, array $args = [], array $input = [])
    {
        $objects = [];
        foreach($commands as $command) {
            $objects[] = $this->getCommand($command);
        }

        $procedure = new Procedure('test:procedure', 'This is a test', $objects);
        $this->app->add($procedure);

        return $this->executeCommand($procedure, $args, $input);
    }

    /**
     * Get a register Command object by classname or command signature
     *
     * @param string $command
     *
     * @return Symfony\Component\Console\Command\Command $command
     */
    private function getCommand(string $command)
    {
        /* Command is classname */
        if(is_subclass_of($command, JinitializeCommand::class)) {

            /* Register the class for test plugin */
            $this->app->registerCommands('test', [$command]);

            /* Create new class object */
            return new $command('test');
        }

        /* Command is signature call */
        return $this->app->find($command);
    }

    private function executeCommand(Command $command, array $args = [], array $input = [])
    {
        $name = $command->getName();
        $command = $this->app->find($name);

        $tester = new CommandTester($command);
        $tester->setInputs($input);
        $tester->execute(array_merge($args, ['command' => $name]));

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
