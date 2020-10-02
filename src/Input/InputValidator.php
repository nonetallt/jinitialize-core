<?php

namespace Nonetallt\Jinitialize\Input;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class InputValidator
{
    private $definition;

    public function __construct(InputDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function requiredArguments()
    {
        return array_filter($this->definition->getArguments(), function($arg) {
            return $arg->isRequired();
        });
    }

    public function requiredOptions() 
    {
        return array_filter($this->definition->getOptions(), function($option) {
            return $option->isValueRequired();
        });
    }

    public function requiredArgumentNames()
    {
        $params = [];
        foreach($this->requiredArguments() as $param) {
            $params[] = $param->getName();
        }
        return $params;
    }

    public function requiredOptionNames()
    {
        $params = [];
        foreach($this->requiredOptions() as $param) {
            $params[] = $param->getName();
        }
        return $params;
    }

    public function missingArguments(InputInterface $input)
    {
        $missing = [];
        foreach($this->requiredArgumentNames() as $required) {

            /* Check if the key is missing or value is null */
            $keyMissing = ! $input->hasArgument($required);
            $valueMissing = $keyMissing ? true : is_null($input->getArgument($required));

            if($keyMissing || $valueMissing) {
                $missing[] = $required;
            } 
        }
        return $missing;
    }

    public function missingOptions(InputInterface $input)
    {
        $missing = [];
        foreach($this->requiredOptionNames() as $required) {

            /* Check if the key is missing or value is null */
            $keyMissing = ! $input->hasOption($required);
            $valueMissing = $keyMissing ? true : is_null($input->getOption($required));

            if($keyMissing || $valueMissing) {
                $missing[] = $required;
            } 
        }
        return $missing;
    }

    private function validateParameters(StringInput $input)
    {
        $this->errors->setContext($this);
        /* Validate options */
        foreach($this->requiredOptions() as $required) {
            if(! in_array($required, array_keys($input->getOptions()))) {
                $requiredParamName = $required->getName();
                $this->errors->fatal("Missing required option '$requiredParamName'.");
            } 
        }
    }
}
