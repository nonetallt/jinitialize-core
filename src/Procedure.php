<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Helper\Table;

use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Procedure\ProcedureValidator;
use Nonetallt\Jinitialize\Common\Traits\AbortsExecution;

class Procedure extends Command
{
    use AbortsExecution;

    private $commands;
    private $commandsExecuted;
    private $description;
    private $validator;

    public function __construct(string $name, string $description, array $commands)
    {
        $this->description = $description;
        parent::__construct($name);

        $this->setCommands($commands);
        $this->commandsExecuted = [];
        $this->validator = new ProcedureValidator($this);
    }

    /**
     * Implemented from Command
     */
    protected function configure()
    {
        $this->setDescription($this->description);
    }

    /**
     * Implemented from Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $this->validator->validate($input, $output, $style);

        /* Execute all commands */
        foreach($this->commands as $command) {
            $success = $this->executeCommand($command, $input, $output, $style);
        }

        /* Print empty line before success */
        $output->writeLn('');
        $style->success("Procedure $this completed");
        return true;
    }

    private function executeCommand(Command $command, $input, $output, $style)
    {
        try {
            $this->commandsExecuted[] = $command;
            /* Write executed command signature */
            $output->writeLn((string)($command));
            $command->run($command->getInput(), $output);
        }
        catch(CommandAbortedException $e) {

            /* If not successful, revert the changes */
            $style->warning("Command {$command->getName()} failed! Reverting changes.");
            $style->error($e->getMessage());
            $this->revert();
            $this->abort("Command within procedure $this failed", $e);
        }
    }

    /**
     * Reverts executed commands
     *
     */
    private function revert($style)
    {
        /* Revert commands in backwards order */
        for($n = count($this->commandsExecuted); $n > 0; $n--) {
            $command = $this->commandsExecuted[$n-1];

            /* Revert if possible */
            if($command->hasPublicMethod('revert')) {
                $command->revert();
                continue;
            }
            $style->warning("Command {$command->getName()} cannot be reverted, revert method is not defined");
        }
        $this->commandsExecuted = [];
    }

    /**
     * @return bool $recommendsRoot Is root recommended to execute this procedure
     */
    public function recommendsRoot()
    {
        foreach($this->commands as $command) {
            if($command->hasPublicMethod('recommendsRoot')) {
                if($command->recommendsRoot()) return true;
            }
        }
        return false;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    private function setCommands(array $commands)
    {
        $class = JinitializeCommand::class;
        $existingCommands = [];
        
        foreach($commands as $command) {
            if(! is_subclass_of($command, $class, false)) {
                throw new \Exception("Commands given to a procedure class should be subclasses of $class");
            } 

            if(in_array($command, $existingCommands, true)) {
                $name = $command->getName();
                $msg = "A procedure should never be initialized with duplicate command objects ($name)";
                throw new \Exception($msg);
            }
            $command->setBelongsToProcedure(true);
            $existingCommands[] = $command;
        }
        $this->commands = $commands;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getValidator()
    {
        return $this->validator;
    }
}
