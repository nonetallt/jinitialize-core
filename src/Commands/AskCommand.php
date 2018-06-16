<?php

namespace Nonetallt\Jinitialize\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Nonetallt\Jinitialize\JinitializeCommand as Command;

class AskCommand extends Command
{

    protected function configure()
    {
        $this->setName('ask');
        $this->setDescription('Ask user to input a value.');
        $this->setHelp('Used to prompt user for some dynamic input. The values are stored in application container and can be used as process placeholder parameters.');

        $this->addArgument('key', InputArgument::REQUIRED, 'The shell command to run.');

        $this->addOption('prepend', 'p', InputOption::VALUE_OPTIONAL, 'Value prepended before the user input.');
        $this->addOption('append', 'a', InputOption::VALUE_OPTIONAL, 'Value appended after the user input.');
        $this->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'The message shown to user when asking for a value.');
    }

    protected function handle($input, $output, $style)
    {
        $key = $input->getArgument('key');
        $message = $input->getOption('message') ?? "Give a value for key '$key'";
            
        $userInput = $style->ask($message);

        /* Add prepend and append options to the value */
        $value = $input->getOption('prepend') . $userInput . $input->getOption('append');

        $this->export($key, $value);
    }

    public function revert()
    {
        // Nothing to revert..
    }

    public function recommendsRoot()
    {
        return false;
    }
}
