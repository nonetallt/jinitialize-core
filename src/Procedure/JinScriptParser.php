<?php

namespace Nonetallt\Jinitialize\Procedure;

class JinScriptParser
{
    private $filepath;
    private $description;
    private $help;
    private $plugins;
    private $isParsed;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->isParsed = false;
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
        $name = explode_multiple($line, ' ', '   ')[0];
        $plugin = new JinScriptPluginParser($name);
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
}
