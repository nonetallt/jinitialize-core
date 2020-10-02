<?php

namespace Nonetallt\Jinitialize\Procedure;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Helpers\ShellUser;
use Nonetallt\Jinitialize\JinScript\JinScriptErrors;

class ProcedureValidator
{
    private $procedure;
    private $errors;

    public function __construct(Procedure $procedure)
    {
        $this->procedure = $procedure;
    }

    /**
     * Checks for before running the procedure:
     * 
     * 1. JinScript syntax errors
     * 2. Missing plugins
     * 3. Missing commands
     * 4. Missing parameters
     * 5. Missing .env values
     * 6. Imported values that do not exist after running the commands before
     *    the requiring command
     *
     */
    public function validate(InputInterface $input, OutputInterface $output, SymfonyStyle $style)
    {
        /* JinScript errors from parsing .jin files */
        $this->validateParsingErrors();

        /* Validate command parameters */
        $this->validateParameters();

        /* Make's sure this procedure does not have commands that would require
         execution of another command that is not executed before.*/ 
        $this->validateRequiresExecution();

        /* Print notification message if there are missing ENV placeholders */
        $this->notifyMissingEnvPlaceholders($output, $style);

        /* Check that the whole procedure can be executed by the user */
        $this->checkPermissions($style);

        /* Print list of commands that are recommended for running before others */
        $this->recommend($output, $style);
    }

    private function validateParsingErrors()
    {
        if(is_null($this->errors)) return;

        if($this->errors->isFatal()) {
            $this->procedure->abort($this->errors);
        }
    }

    private function validateParameters()
    {
        $fatal = false;
        $errors = '';

        foreach($this->procedure->getCommands() as $command) {
            /* if($command->getName() === 'core:shell') { */
            /*     dd($command->getErrors()); */
            /* } */
            $errors = $command->getErrors();
            if($errors->isFatal()) $fatal = true;
            $errors .= (string)$errors;
        }

        if($fatal) {
            $this->procedure->abort($errors);
        }
    }

    private function validateRequiresExecution()
    {
        $errors = [];
        $method = 'requiresExecuting';
        $commandsThatWillExecuteBefore = [];

        foreach($this->procedure->getCommands() as $command) {

            /* Save commands that are executed before next command */
            /* Make sure that the command classes are compared instead of objects */
            $commandsThatWillExecuteBefore[] = get_class($command);
            
            /* Skip methods that don't have the require method */
            /* Executed methods should be saved before this in case they dont' have the method */
            if(! $command->hasPublicMethod($method)) continue;

            $notExecuted = array_diff($command->$method(), $commandsThatWillExecuteBefore);

            /* If there are no problems, skip to next */
            if(empty($notExecuted)) continue;

            /* Construct error message */
            $name = get_class($command);
            $str = implode(', ', $notExecuted);
            $errors[] = "$name: $str";
        }

        if(!empty($errors)) {
            $pName = $this->procedure->getName();

            $message = "Procedure $pName has commands that require other commands to be executed first:";
            $message .= PHP_EOL;
            $message .= implode(PHP_EOL, $errors);
            $this->procedure->abort($message);
        }
    }

    private function notifyMissingEnvPlaceholders($output, $style)
    {
        $print = false;
        $rows = [];
        foreach($this->procedure->getCommands() as $command) {
            foreach($command->missingEnv() as $key => $value) {
                $rows[] = [(string)$command, $value];
                $print = true;
            }
        }

        /* Empty table should not be printed */
        if(! $print) return;

        $style->note("Procedure $this->procedure has ENV placeholders that cannot be resolved at initialization.");

        $table = new Table($output);
        $table->setHeaders(['command', 'missing']);
        $table->setRows($rows);
        $table->render();

        /* Write empty line after table */
        $output->writeLn('');
    }

    /** 
     * Warn user if procedure should be ran as root and is being ran by some other user 
     *
     */
    private function checkPermissions($style)
    {
        $warnings = [];

        foreach($this->procedure->getCommands() as $command) {

            /* Skip evalutating commands that do not recommend root */
            if(! $command->hasPublicMethod('recommendsRoot')) continue;

            if($command->recommendsRoot() && ! ShellUser::getInstance()->isRoot()) {
                $name = $command->getName();
                $user = ShellUser::getInstance()->getName();
                $warnings[] = "Command $name recommends running as root, currently $user.";
            }
        }

        if(!empty($warnings)) {
            $style->warning($warnings);
            if($style->confirm("Would you like to abort current procedure ({$this->procedure->getName()})?")) {
                $this->abort("Procedure aborted by user");
            }
        }
    }

    private function recommend($output, $style)
    {
        /* Similar to validate */
        $rows = [];
        $executed = [];
        $method = 'recommendsExecuting';
        
        foreach($this->procedure->getCommands() as $command) {
            $executed[] = get_class($command);
            if(! $command->hasPublicMethod($method)) continue;

            $notExecuted = array_diff($command->$method(), $executed);

            foreach($notExecuted as $recommended) {
                $rows[] = [$command->getPluginName(), $command->getName(), $recommended];
            }
        }

        if(empty($rows)) return;

        $style->note("Procedure $this->procedure has commands that recommend running the following commands before their execution:");
        $table = new Table($output);
        $table->setHeaders(['plugin', 'method', 'recommends']);
        $table->setRows($rows);
        $table->render();

        /* Write empty line after table */
        $output->writeLn('');
    }

    public function setErrors(JinScriptErrors $errors)
    {
        $this->errors = $errors;
    }
}
