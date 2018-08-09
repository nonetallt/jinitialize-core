<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Procedure\JinScript;
use Nonetallt\Jinitialize\Procedure\JinScriptParser;

class JinScriptParserTest extends TestCase
{
    use CleansOutput;

    private $filepath;

    public function setUp()
    {
        $this->file = $this->inputFolder('php-package.jin');
        $this->parser = new JinScriptParser($this->file);
    }

    public function testNameReturnsBasenameOfTheFile()
    {
        /* PLACEHOLDER */
        $script = new JinScript($this->file);
        $this->assertEquals('php-package', $script->name());
    }
    
    public function testDescription()
    {
        $this->assertEquals("Create a new generic php package", $this->parser->getDescription());
    }

    public function testHelp()
    {
        $this->assertEquals("Longer help description here..", $this->parser->getHelp());
    }

    public function testPlugins()
    {
        $this->assertCount(4, $this->parser->getPlugins());
    }
}
