<?php

namespace Nonetallt\Jinitialize\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Nonetallt\Jinitialize\JinitializeCommand as Command;

class ShellCommand extends Command
{

    protected function configure()
    {
        $this->setName('shell');
        $this->setDescription('Executes a command using command line.');
        $this->addArgument('shell_command', InputArgument::REQUIRED, 'The shell command to run.');
        $this->addArgument('export', InputArgument::OPTIONAL, 'Name to export command output as.');
    }

    protected function handle($input, $output, $style)
    {
        $process = new Process($input->getArgument('shell_command'));

        $process->run(function($type, $buffer) use($output){
            if($type !== Process::ERR) {
                $output->write($buffer);
            }
        });

        if(! $process->isSuccessful()) {
            $exception = new ProcessFailedException($process);
            $this->abort($exception->getMessage());
        }

        if(! is_null($input->getArgument('export'))) {
            $this->export($input->getArgument('export'), $process->getOutput());
        }
    }

    public function recommendsRoot()
    {
        return false;
    }
}
