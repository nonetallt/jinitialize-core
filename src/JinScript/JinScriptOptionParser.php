<?php

namespace Nonetallt\Jinitialize\JinScript;

class JinScriptOptionParser
{
    private $option;

    public function __construct(string $option)
    {
        $this->option = $option;
    }

    /**
     * Prepend -- before option
     * Quote option value after = 
     */
    public function format()
    {
        $option = $this->option;
        
        /* Find first = symbol */
        $firstEquals = strpos($option, '=');

        /* If symbol does not exist the option is invalid */
        if($firstEquals === false) {
            throw new \Exception("Option syntax error: option '$option' must contain the = symbol.");
        }

        $optionName = substr($option, 0, $firstEquals);
        $optionValue = substr($option, $firstEquals + 1);

        /* Add quatations to option value */
        if(! starts_with($optionValue, "'", '"')) $optionValue = "'$optionValue";
        if(! ends_with($optionValue, "'", '"')) $optionValue = "$optionValue'";

        /* Merge name and value back together */
        $option = "$optionName=$optionValue";

        /* Prepend slashes until there is 2 before option */
        while(! starts_with($option, '--')) {
            $option = "-$option";
        }

        return $option;
    }
}
