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
        $this->app = new JinitializeApplication();

        parent::__construct();
    }

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
        $command = $this->app->find($command->getName());
        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName()
        ]);
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
}
