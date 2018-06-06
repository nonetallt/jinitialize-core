<?php

namespace Nonetallt\Jinitialize\Testing;

use PHPunit\Framework\TestCase as Test;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\ArrayInput;

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
    protected function registerLocalPlugin(string $composerPath)
    {
        $composer = [];

        if(file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
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
     * @return Nonetallt\Jinitialize\Testing\CommandTester
     *
     */
    protected function runCommand(string $command, array $args = [], array $input = [])
    {
        $command = $this->getCommand($command);
        $arguments = $command->getInput()->getArguments();
        $options = [];

        /* Append the 2 dashes before each option name before using them as input for tester */
        foreach($command->getInput()->getOptions() as $key => $value) {
            $options["--$key"] = $value;
        }

        $args = array_merge($args, $arguments, $options);
        return $this->executeCommand($command, $args, $input);
    }

    protected function runProcedure(string $procedure)
    {
        $procedure = $this->app->get($procedure);
        return $this->executeCommand($procedure);
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
        $parts = explode(' ', $command, 2);

        /* Find the command using signature */
        $command =  $this->app->getNew($parts[0]);

        /* 
          The found command should be ovewritten in case it has been already run. 
          The application will add command name to input definition arguments
          and will mess up the arguments list otherwise.
         */
        $this->app->add($command);

        /* Get part of string after a space, containing all args */ 
        $argString = $parts[1] ?? '';

        /* Save args to command for ease of access */
        $command->setInput(new StringInput($argString));

        return $command;
    }

    private function executeCommand(Command $command, array $args = [], array $input = [])
    {
        $name = $command->getName();
        $command = $this->app->get($name);

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
