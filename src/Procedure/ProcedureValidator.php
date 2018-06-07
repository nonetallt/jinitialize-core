<?php

namespace Nonetallt\Jinitialize\Procedure;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;

use Nonetallt\Jinitialize\Procedure;

class ProcedureValidator
{
    private $procedure;

    public function __construct(Procedure $procedure)
    {
        $this->procedure = $procedure;
    }

    public function validate(InputInterface $input, SymfonyStyle $style)
    {
        /* Make's sure this procedure does not have commands that would require
         execution of another command that is not executed before.*/ 
        $this->validateRequiresExecution();
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
            throw $e;
        }
    }
}
