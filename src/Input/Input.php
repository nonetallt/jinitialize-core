<?php

namespace Nonetallt\Jinitialize\Input;

use Symfony\Component\Console\Input\InputInterface;
use Nonetallt\Jinitialize\JinitializeContainer;

class Input
{
    private $input;
    private $format;

    public function __construct(InputInterface $input, string $format = '[$]')
    {
        $this->input = $input;
        $this->format = $format;
    }

    public function setFormat(string $format)
    {
        if(strpos($format, '$') === false) {
            throw new \Exception("Format $format must contain placeholder name denoted by $.");
        }
        $this->format = $format;
    }

    public function replaceEnvPlaceholders()
    {
        $this->walk(function($key, $value) {
            $value = $this->replaceEnvPlaceholder($value);
            self::setValue($this->input, $key, $value);
        });
    }

    public function replaceExportedPlaceholders()
    {
        $this->walk(function($key, $value) {
            $value = $this->replaceExportedPlaceholder($value);
            self::setValue($this->input, $key, $value);
        });
    }

    public function getPlaceholders()
    {
        return array_merge($this->getEnvPlaceholders(), $this->getExportedPlaceholders());
    }

    public function getEnvPlaceholders()
    {
        /* Require that none of the characters are : */
        $regex = '([^:]+)';

        return $this->findPlaceholders($regex);
    }

    public function getExportedPlaceholders()
    {
        /* Require that there are two wildcard string separated by : */
        $regex = '(.+:.+)';

        return $this->findPlaceholders($regex);
    }

    private function findPlaceholders(string $regex)
    {
        $placeholders = [];
        foreach($this->values() as $key => $value) {
            $matches = [];
            preg_match($this->formatRegex($regex), $value, $matches);

            /* Remove full match from results so only capture group is left */
            unset($matches[0]);
            $placeholders = array_merge($placeholders, $matches);
        }
        return $placeholders;
    }

    private function formatRegex(string $regex)
    {
        /* Quote regext to use exact match */
        $search = preg_quote($this->format);

        /* Change placeholder name to wildcard matching 1 or more characters */
        $search = str_replace('\$', $regex, $search);

        return "|$search|";
    }

    private static function setValue(InputInterface $input, string $name, $value)
    {
        if($input->hasOption($name)) $input->setOption($name, $value);
        else if($input->hasArgument($name)) $input->setArgument($name, $value);
        else throw new \Exception("Value $name could not be set, not an argument or option.");
    }

    private function getSearch($key)
    {
        return str_replace('$', $key, $this->format);
    }

    private function replaceEnvPlaceholder($subject)
    {
        /* Try searching placeholders for each env value */
        foreach($_ENV as $key => $value) {

            /* Search by placeholder format */
            $search = $this->getSearch($key);

            /* Do not try replacing when placeholder does not exist */
            if(strpos($subject, $search) === false) continue;

            $subject = str_replace($search, $value, $subject);
        }
        return $subject;
    }

    private function replaceExportedPlaceholder($subject)
    {
        foreach(JinitializeContainer::getInstance()->getData() as $plugin => $data) {
            foreach($data as $key => $value) {
                $subject = str_replace("[$plugin:$key]", $value, $subject);
            }
        }
        return $subject;
    }

    /** 
     * Apply a callback to each input value 
     */
    private function walk(callable $cb)
    {
        foreach($this->values() as $key => $value) {
            $cb($key, $value);
        }
    }

    public function values()
    {
        return array_merge($this->input->getArguments(), $this->input->getOptions());
    }
}
