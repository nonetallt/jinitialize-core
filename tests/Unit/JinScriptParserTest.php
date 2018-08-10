<?php

namespace Tests\Unit;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\JinScript\JinScriptParser;
use Nonetallt\Jinitialize\Testing\TestCase;

class JinScriptParserTest extends TestCase
{
    use CleansOutput;

    private $filepath;

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin($this->projectRoot().'/composer.json');
        $this->file = $this->inputFolder('php-package.jin');
        $this->parser = new JinScriptParser($this->getApplication(), $this->file);
    }

    public function testNameReturnsBasenameOfTheFile()
    {
        $this->assertEquals('php-package', $this->parser->getName());
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
