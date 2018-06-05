<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputOption;

use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Helpers\ShellUser;

abstract class JinitializeCommand extends Command
{
    private $user;
    private $plugin;
    private $input;
    private $belongsToProcedure;
    private $isExecuted;

    public function __construct(string $plugin)
    {
        parent::__construct();
        $this->user               = ShellUser::getInstance();
        $this->plugin             = $plugin;
        $this->input              = new ArrayInput([]);
        $this->belongsToProcedure = false;
        $this->isExecuted         = false;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->isExecuted = true;
        $style = new SymfonyStyle($input, $output);
        $this->handle($input, $output, $style);
    }

    protected function abort(string $message)
    {
        $exception = new CommandAbortedException($message);
        $exception->setCommand($this);
        throw $exception;
    }

    /**
     * Save a value to the application container
     * 
     */
    protected function export(string $key, $value)
    {
        $container = JinitializeContainer::getInstance();
        $container->getPlugin($this->getPluginName())->getContainer()->set($key, $value);
    }

    /**
     * Get a value from the application container
     *
     */
    protected function import(string $plugin, string $key)
    {
        $container = JinitializeContainer::getInstance();
        return $container->getPlugin($this->getPluginName())->getContainer()->get($key);
    }

    protected function getUser()
    {
        return $this->user;
    }

    public function getPluginName()
    {
        return $this->plugin;
    }

    protected function getContainerData(string $plugin = null)
    {
        $container = JinitializeContainer::getInstance();

        if(! is_null($plugin)) {
            $container = $container->getPlugin($plugin)->getContainer();
        }

        return $container->getData();
    }

    protected function wasOptionPassed(InputInterface $input, string $name)
    {
        if(! $input->hasOption($name)) return false;

        $option = $this->getDefinition()->getOption($name);

        /* Input option none has false for both */
        if($option->isValueRequired() || $option->isValueOptional()) {
            $msg = "wasOptionPassed() should not be used for options with input option types other than VALUE_NONE";
            throw new \Exception($msg);
        }

        return $input->getOption($name) !== $option->getDefault();
    }

    private function getContainer()
    {
        return JinitializeContainer::getInstance()->getPlugin($this->getPluginName())->getContainer();
    }

    /**
     * Used by procedure to get input for command signatures
     */
    public function getInput()
    {
        return $this->input;
    }

    public function setInput(StringInput $input)
    {
        /* Bind args array keys to input definition of the command */
        $input->bind($this->getDefinition());

        /* Convert string input to array */
        $args = $input->getArguments();
        $options = [];

        /* Append the 2 dashes before each option name */
        foreach($input->getOptions() as $key => $value) {
            $options["--$key"] = $value;
        }

        /* Bind the array input to this command definition */
        $this->input = new ArrayInput(array_merge($input->getArguments(), $options));
        $this->input->bind($this->getDefinition());
    }

    public function setBelongsToProcedure(bool $bool)
    {
        $this->belongsToProcedure = $bool;
    }

    public function belongsToProcedure()
    {
        return $this->belongsToProcedure;
    }

    /**
     * @param string $method Name of the queried method
     *
     * @return bool Wether this object has a public method called $method
     */
    public function hasPublicMethod(string $method)
    {
        if(! method_exists($this, $method)) return false;

        $reflection = new \ReflectionMethod($this, $method);
        if(! $reflection->isPublic()) return false;

        return true;
    }

    public function isExecuted()
    {
        return $this->isExecuted;
    }

    public function __toString()
    {
        return $this->getName();
    }

    protected abstract function handle($input, $output, $style);

    /* public abstract function revert(); */

    /* public abstract function recommendsRoot(); */

    /* public abstract function exportsVariables(); */
}
