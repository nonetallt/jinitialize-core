<?php

namespace Nonetallt\Jinitialize;
use Dotenv\Dotenv;

class Installer
{

    public static function boot()
    {
        $dotenv = new Dotenv(__DIR__ . '/..');
        $dotenv->load();

        $test = env('asd', 'default');
        /* var_dump($test); */

        /* $stub = new StubGenerator(__DIR__ . '/../stubs/apache2-site.stub'); */
        /* $stub->render([ */
        /*     '[APP_NAME]'  => '', */
        /*     '[APP_PATH]'  => '', */
        /*     '[SITE_NAME]' => '', */
        /* ]) */

        return true;
    }

    public function installApache2Site()
    {
        $this->confirmVariable('SITE_NAME', 'placeholder');

        /* $input = __DIR__ . '/../../stubs/apache2-site.stub'; */
        /* $output = __DIR__ . "/../output/$site.out"; */

        /* $stub = new StubGenerator($input, $output); */
        /* $test = $stub->render([ */
        /*     '[APP_NAME]'  => env('APP_NAME'), */
        /*     '[APP_PATH]'  => env('APP_PATH'), */
        /*     '[SITE_NAME]' => env('SITE_NAME'), */
        /* ]); */
    }

    private function confirmVariable(string $name, string $askMessage)
    {
        $envVariable = env($name);

        if(is_null($envVariable)) {
            $envVariable = $this->askUntillValid("Configure $name: " ,function($input) {
                return file_exists($input);
            });
        }

        if(! is_string($envVariable)) {
            throw new \Exception('Invalid');
        }

        /* Confirm selection */
        $this->askYesNo("Are you sure you want to set $name = $envVariable?");

        return $envVariable;
    }

    /**
     * Prompt user to supply either a yes or no answer
     *
     * @param string $prompt the prompt message
     * @return bool user answer, y = true : n = false
     *
     */
    public function askYesNo(string $prompt)
    {
        $prompt .= ' (y/n): ';
        $result = null;

        $result = $this->askUntillValid($prompt, function($answer) {
            /* Lowercase user prompt */
            $answer = strtolower($answer);

            /* Correrct answer is y or n */
            return $answer === 'y' || $answer === 'n';
        });

        return $result === 'y';
    }
    
    /**
     * Prompt user for input untill the given input returns true from the
     * validation callback function.
     *
     * @param string $prompt the prompt message
     * @param callable $validationCallback
     * @return $result user input that passed validation
     *
     */
    public function askUntillValid(string $prompt, callable $validationCallback)
    {
        $result = null;

        while($validationCallback($result) !== true) {
            $result = $this->readLine("$prompt");
        }
        return $result;
    }

    /**
     * Get user input from commandline
     */
    private function readline(string $prompt)
    {
        if (PHP_OS == 'WINNT') {
            echo $prompt;
            $line = stream_get_line(STDIN, 1024, PHP_EOL);

        } else {
            $line = readline($prompt);
        }
        return $line;
    }
}
