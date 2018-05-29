<?php

namespace Nonetallt\Jinitialize;

use Nonetallt\Jinitialize\Helpers\Strings;
use Nonetallt\Jinitialize\Exceptions\PluginNotFoundException;

class ProcedureFactory
{
    private $paths;
    private $parsed;
    private $app;

    public function __construct(JinitializeApplication $app, array $paths)
    {
        $this->app = $app;
        /* Filepaths for .json files */
        $this->paths = $paths;
        $this->parsed = [];
    }

    public function create(string $procedure)
    {
        /* Get array json data from the file containing the procedure */
        $json = $this->parseUntilFound($procedure);

        if(is_null($json)) {
            throw new \Exception("Procedure '$procedure' was not found");
        }

        /* Return the named procedure in case file contains multiples */
        foreach($json as $name => $data) {
            if($name === $procedure) return $this->procedureFromArray($name, $data);
        }
    }

    /**
     * Note: the given json might contain multiple procedures defined in 1 file
     *
     */
    public function procedureFromArray(string $name, array $json)
    {
        $description = 'No description found';
        $parsedCommands = [];

        if(isset($json['description'])) {
            $description = $json['description'];
        }

        if(isset($json['commands'])) {
            $parsedCommands = $this->parseCommands($json['commands'], $name);
        }

        return new Procedure($name, $description, $parsedCommands);
    }

    /**
     * Create command classes from a json array
     */
    private function parseCommands(array $json, string $procedureName)
    {
        $factory = new CommandFactory($this->app);
        $errors = [];

        foreach($json as $commandString) {

            /* Get the namespace of the command */
            $plugin = explode(':', $commandString, 2)[0];

            /* Check that the given plugin is installed */
            if(! $this->app->getContainer()->hasPlugin($plugin)) {
                $errors[] = "Plugin '$plugin' is required to run procedure '$procedureName'.";
                continue;
            }
            $parsedCommands[] = $factory->create($plugin, $commandString);
        }

        if(! empty($errors)) {
            throw new PluginNotFoundException(implode(PHP_EOL, $errors));
        }
        return $parsedCommands;
    }

    /**
     * Parse paths until file containing the given procedure if found
     * or there is nothing left to parse.
     *
     */
    private function parseUntilFound(string $procedure) 
    {
        /* First check if parsed content has the desired procedure */
        foreach($this->parsed as $path => $json) {
            if($this->hasProcedure($json, $procedure)) return $json; 
        }

        /* Next, check if the most likely file has the procedure */
        $json = $this->findMostLikelyFile($procedure);
        if(! is_null($json)) return $json;
        

        /* Lastly, check rest of the files one by one */
        foreach($this->unparsedPaths() as $path) {
            $json = $this->parsePath($path);
            if($this->hasProcedure($json, $procedure)) return $json; 
        }

        return null;
    }

    /**
     * Find the file that is most likely to contain the given procedure.
     * Prioritizes files with the same name as the procedure.
     *
     */
    private function findMostLikelyFile(string $procedure)
    {
        foreach($this->paths as $path) {

            $filename = Strings::afterLast($path, '/');
            $filenameWithoutExtension = Strings::untilLast($filename, '.');

            if($procedure === $filenameWithoutExtension) {
                return $filename;
            }
        }
        return null;
    }

    /**
     * @return array $unparsed A list of filepaths that has not been parsed
     *
     */
    private function unparsedPaths()
    {
        return array_diff($this->paths, array_keys($this->parsed));
    }

    private function parsePath(string $path)
    {
        if(! is_file($path)) {
            throw new \Exception("Can't read path $path, not a file");
        }
        $json = json_decode(file_get_contents($path), true);

        if(is_null($json)) {
            throw new \Exception("Procedure json at path $path could not be parsed");
        }

        $this->parsed[$path] = $json;

        return $json;
    }

    private function hasProcedure(array $content, string $procedure)
    {
        return in_array($procedure, array_keys($content));
    }

    /**
     * Get a list of all procedure names contained in the factory's filepaths
     * @return array $procedureNames array of procedure names
     *
     */
    public function getNames()
    {
        $procedureNames = [];

        /* Parse all unparsed paths */
        foreach($this->unparsedPaths() as $path) {
            $json = $this->parsePath($path);
        }

        /* Get all parsed names */
        foreach($this->parsed as $path => $procedures) {
            foreach($procedures as $name => $procedure) {
                $procedureNames[] = $name;
            }
        }

        return $procedureNames;
    }
}
