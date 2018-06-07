<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;

use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;
use Nonetallt\Jinitialize\Procedure\ProcedureValidator;
use Nonetallt\Jinitialize\Common\Traits\AbortsExecution;

class Procedure extends Command
{
    use AbortsExecution;

    private $commands;
    private $commandsExecuted;
    private $name;
    private $description;
    private $io;
    private $validator;

    public function __construct(string $name, string $description, array $commands)
    {
        $this->name = $name;
        $this->description = $description;
        $this->io = null;

        parent::__construct();

        $this->setCommands($commands);
        $this->commandsExecuted = [];
        $this->validator = new ProcedureValidator($this);
    }
    

    /**
     * Implemented from Command
     */
    protected function configure()
    {
        $this->setName($this->name);
        $this->setDescription($this->description) ;
    }

    /**
     * Implemented from Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->validator->validate($input, $this->io);



        /* TODO */
        $this->missingEnv($output);

        $app = $this->getApplication();

        /* Print list of commands that are recommended for running before others */
        $this->recommend($output);

        /* Check that the whole procedure can be executed by the user */
        if($this->checkPermissions($this->io)) return false;


        foreach($this->commands as $command) {

            try {
                $this->commandsExecuted[] = $command;
                $output->writeLn((string)($command));
                $command->run($command->getInput(), $output);
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

        /* Print empty line before success */
        $output->writeLn('');
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

            if($command->hasPublicMethod('revert')) {
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
            if($command->hasPublicMethod('recommendsRoot')) {
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
            if(! $command->hasPublicMethod('recommendsRoot')) continue;

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

    private function validate()
    {
    }

    private function recommend(OutputInterface $output)
    {
        /* Similar to validate */
        $rows = [];
        $executed = [];
        $method = 'recommendsExecuting';
        
        foreach($this->commands as $command) {
            $executed[] = get_class($command);
            if(! $command->hasPublicMethod($method)) continue;

            $notExecuted = array_diff($command->$method(), $executed);

            foreach($notExecuted as $recommended) {
                $rows[] = [$command->getPluginName(), $command->getName(), $recommended];
            }
        }

        if(empty($rows)) return;

        $this->io->note("Procedure $this has commands that recommend running the following commands before their execution:");
        $table = new Table($output);
        $table->setHeaders(['plugin', 'method', 'recommends']);
        $table->setRows($rows);
        $table->render();

        /* Write empty line after table */
        $output->writeLn('');
    }

    public function missingEnv(OutputInterface $output)
    {
        $print = false;
        $rows = [];
        foreach($this->commands as $command) {
            foreach($command->missingEnv() as $key => $value) {
                $rows[] = [(string)$command, $value];
                $print = true;
            }
        }

        /* Empty table should not be printed */
        if(! $print) return;

        $this->io->note("Procedure $this has ENV placeholders that cannot be resolved at initialization.");

        $table = new Table($output);
        $table->setHeaders(['command', 'missing']);
        $table->setRows($rows);
        $table->render();

        /* Write empty line after table */
        $output->writeLn('');
    }

    public function __toString()
    {
        return $this->getName();
    }
}
