<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Nonetallt\Jinitialize\Helpers\ShellUser;

class Procedure extends Command
{
    private $commands;
    private $commandsExecuted;
    private $name;
    private $description;
    private $io;

    public function __construct(string $name, string $description, array $commands)
    {
        $this->name = $name;
        $this->description = $description;
        $this->io = null;

        parent::__construct();

        $this->commands = $commands;
        $this->commandsExecuted = [];
    }

    /**
     * Implemented from Command
     */
    protected function configure()
    {
        $this
            ->setName($this->name)
            ->setDescription($this->description)
            ;
    }

    /**
     * Implemented from Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $app = $this->getApplication();

        $abort = $this->checkPermissions($this->io);
        if($abort) return false;

        foreach($this->commands as $command) {
            $command = $app->find($command->getName());
            $this->commandsExecuted[] = $command;

            try {
                $command->run(new ArrayInput([]), $output);
            }
            catch(CommandAbortedException $e) {

                /* If not successful, revert the changes */
                $this->io->warning("Command {$command->getName()} failed! Reverting changes.");
                $this->io->error($e->getMessage());
                $this->revert();

                /* Stop executing further commands */
                return false;
            }
        }

        $this->io->success("Procedure $this->name completed");
        return true;
    }

    /**
     * Reverts executed commands
     *
     */
    public function revert()
    {
        /* Revert commands in backwards order */
        for($n = count($this->commandsExecuted); $n > 0; $n--) {
            $command = $this->commandsExecuted[$n-1];

            if(method_exists($command, 'revert')) {
                $command->revert();
            }
            else {
                $this->io->warning("Command {$command->getName()} cannot be reverted, revert method is not defined");
            }
        }
        $this->commandsExecuted = [];
    }

    /**
     * @return bool $recommendsRoot Is root recommended to execute this procedure
     */
    public function recommendsRoot()
    {
        foreach($this->commands as $command) {
            if(method_exists($command, 'recommendsRoot')) {
                if($command->recommendsRoot()) return true;
            }
        }
        return false;
    }

    /**
     * Variables exported by all commands in this procedure
     */
    public function exportsVariables()
    {
        $vars = [];
        foreach($this->commands as $command) {
            $vars[] = $command->exportsVariables();
        }
        return $vars;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    /** 
     * Warn user if procedure should be ran as root an is being ran by some other user 
     *
     */
    private function checkPermissions(SymfonyStyle $io)
    {
        $abort = false;
        $warnings = [];

        foreach($this->commands as $command) {

            /* Skip evalutating commands that do not recommend root */
            if(! method_exists($command, 'recommendsRoot')) continue;

            if($command->recommendsRoot() && ! ShellUser::getInstance()->isRoot()) {
                $name = $command->getName();
                $user = ShellUser::getInstance()->getName();
                $warnings[] = "Command $name recommends running as root, currently $user.";
            }
        }

        if(!empty($warnings)) {
            $io->warning($warnings);
            $abort = $io->confirm("Would you like to abort current procedure ({$this->getName()})?");
        }

        return $abort;
    }
}
