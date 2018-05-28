<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;

use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Helpers\ShellUser;

abstract class JinitializeCommand extends Command
{
    private $user;
    private $plugin;
    private $input;

    public function __construct(string $plugin)
    {
        parent::__construct();
        $this->user = ShellUser::getInstance();
        $this->plugin = $plugin;
        $this->input = new StringInput('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        
        $style->title('Initializing ' . $this->getName());
        $this->handle($input, $output, $style);

        $style->success("{$this->getName()} initialized successfully");
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

    private function getContainer()
    {
        return JinitializeContainer::getInstance()->getPlugin($this->getPluginName())->getContainer();
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    protected abstract function handle($input, $output, $style);

    /* public abstract function revert(); */

    /* public abstract function recommendsRoot(); */

    /* public abstract function exportsVariables(); */
}
