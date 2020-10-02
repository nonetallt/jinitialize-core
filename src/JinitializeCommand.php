<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Exception\RuntimeException;

use Nonetallt\Jinitialize\Helpers\ShellUser;
use Nonetallt\Jinitialize\Input\Input;
use Nonetallt\Jinitialize\Common\Traits\AbortsExecution;
use Nonetallt\Jinitialize\JinScript\JinScriptErrors;
use Nonetallt\Jinitialize\Input\InputValidator;

abstract class JinitializeCommand extends Command
{
    use AbortsExecution;

    private $pluginName;
    private $input;
    private $belongsToProcedure;
    private $isExecuted;
    private $argString;
    private $errors;

    public function __construct(string $pluginName)
    {
        parent::__construct();
        $this->pluginName         = $pluginName;
        $this->input              = new ArrayInput([]);
        $this->belongsToProcedure = false;
        $this->isExecuted         = false;
        $this->argString          = '';
        $this->errors             = new JinScriptErrors($this);
    }

    private function replacePlaceholders($input)
    {
        $in = new Input($input, $_ENV['JINITIALIZE_PLACEHOLDER_FORMAT'] ?? '[$]');
        $in->replaceEnvPlaceholders();
        $in->replaceExportedPlaceholders();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->replacePlaceholders($input);
        $this->isExecuted = true;
        $style = new SymfonyStyle($input, $output);

        try {
            $this->handle($input, $output, $style);
        } 
        catch(\Exception $e) {
            $this->abort($e->getMessage(), $e);
        }
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
     * Get a value from the local plugin container
     *
     */
    protected function import(string $key)
    {
        $container = JinitializeContainer::getInstance();
        return $container->getPlugin($this->getPluginName())->getContainer()->get($key);
    }

    public function getPluginName()
    {
        return $this->pluginName;
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

    private function validationErrors($input)
    {
        $validator = new InputValidator($this->getDefinition());

        foreach($validator->missingArguments($input) as $missing) {
            $this->errors->fatal("Missing required argument '$missing'.");
        }

        foreach($validator->missingOptions($input) as $missing) {
            $this->errors->fatal("Missing required option '$missing'.");
        }
        return $validator;
    }

    public function setInput(StringInput $input)
    {
        /* Saved for display purposes */
        $this->argString = (string)$input;
        $this->errors->setContext($this);

        /* Bind args array keys to input definition of the command */
        try {
            $input->bind($this->getDefinition());
        }
        catch(RuntimeException $e) {
            /* var_dump($e->getMessage()); */
        }

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

        return new InputValidator($this->getDefinition());
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

    public function missingEnv()
    {
        $input = new Input($this->getInput(), $_ENV['JINITIALIZE_PLACEHOLDER_FORMAT'] ?? '[$]');
        return $input->missingEnv();
    }

    public function isExecuted()
    {
        return $this->isExecuted;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function __toString()
    {
        return $this->getName() .' '. $this->argString;
    }

    protected abstract function handle($input, $output, $style);
}
