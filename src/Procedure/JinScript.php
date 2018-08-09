<?php

namespace Nonetallt\Jinitialize\Procedure;

class JinScript
{
    private $filepath;
    private $parser;

    public function __construct(string $filepath)
    {
        if(! file_exists($filepath)) throw new \Exception("File $filepath not found");
        if(! ends_with($filepath, '.jin')) throw new \Exception("Script should only be created from .jin files");

        $this->filepath = $filepath;
        $this->parser = new JinScriptParser($filepath);
    }

    public function name()
    {
        return basename($this->filepath, '.jin');
    }

    public function description()
    {
        return $this->parser->getDescription();
    }

    public function help()
    {
        return $this->parser->getHelp();
    }
}
