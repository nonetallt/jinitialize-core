<?php

namespace Nonetallt\Jinitialize\JinScript;

use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\Exceptions\PluginNotFoundException;

class JinScriptParser
{
    private $filepath;
    private $description;
    private $help;
    private $plugins;
    private $isParsed;
    private $app;
    private $errors;

    public function __construct(JinitializeApplication $app, string $filepath)
    {
        if(! file_exists($filepath)) throw new \Exception("File $filepath not found");
        if(! ends_with($filepath, '.jin')) throw new \Exception("Script should only be created from .jin files");
        $this->filepath = $filepath;
        $this->isParsed = false;
        $this->app = $app;
        $this->errors = new JinScriptErrors();
        $this->plugins = [];
    }

    public function createProcedure()
    {
        return new Procedure(
            $this->getName(),
            $this->getDescription(),
            $this->getCommands(),
            $this->getHelp()
        );
    }

    /**
     * Private since files should only be parsed when neccesary
     */
    private function parse()
    {
        $handle = fopen($this->filepath, 'r');
        if(! $handle) throw new \Exception("Can't read file $this->filepath");

        $plugin = null;

        while(($line = fgets($handle)) !== false) {

            /* Skip comments and empty lines */
            if(starts_with($line, '#') || trim($line) === '') continue;

            /* Find description setting */
            else if(starts_with($line, 'description')) $this->parseDescription($line);

            /* Find help setting */
            else if(starts_with($line, 'help')) $this->parseHelp($line);

            /* Capture commands */
            else if(starts_with_whitespace($line)) $plugin->parseCommand($line);

            /* Start plugin capture */
            else {
                $plugin = $this->parseCurrentPlugin($line);
            }
        }

        fclose($handle);

        /* Set state as parsed */
        $this->isParsed = true;
    }

    private function parseCurrentPlugin(string $line)
    {
        /* First string delimited by space or tab */
        $pluginName = explode_multiple($line, ' ', '   ')[0];
        $plugin = new JinScriptPluginParser($this->app, $pluginName);

        if(!$plugin->isInstalled()) $this->errors->fatal("Missing required plugin '$pluginName'.");
        
        $this->plugins[] = $plugin;

        return $plugin;
    }

    private function parseDescription(string $line) 
    {
        $this->description = $this->parseDeclaration($line, 'description');
    }

    private function parseHelp(string $line) 
    {
        $this->help = $this->parseDeclaration($line, 'help');
    }

    private function parseDeclaration(string $line, string $name)
    {
        /* Remove first instance of "description" string */
        $desc = substr($line, strlen($name));
        $desc = trim($desc);

        /* Remove surrounding quotations */
        if(starts_with($desc, '"') || starts_with($desc, '\'')) $desc = substr($desc, 1);
        if(ends_with($desc, '"') || ends_with($desc, '\'')) $desc = substr($desc, 0, strlen($desc) -1);

        return $desc;
    }

    public function isParsed()
    {
        return $this->isParsed;
    }

    public function getDescription()
    {
        if(! $this->isParsed()) $this->parse();
        return $this->description;
    }

    public function getHelp()
    {
        if(! $this->isParsed()) $this->parse();
        return $this->help;
    }

    public function getCommands()
    {
        if(! $this->isParsed()) $this->parse();

        $commands = [];
        foreach($this->plugins as $plugin) {
            $commands[] = $plugin->getCommands();
        }
        return $commands;
    }

    public function getPlugins()
    {
        if(! $this->isParsed()) $this->parse();
        return $this->plugins;
    }

    public function getName()
    {
        return basename($this->filepath, '.jin');
    }
}
