<?php

namespace Tests\Unit;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\JinScript\JinScriptParser;
use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Exceptions\CommandAbortedException;

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

    /* Register a procedure for testing purposes */
    private function registerProcedure(string $name)
    {
        $file = $this->inputFolder($name);
        $parser = new JinScriptParser($this->getApplication(), $file);
        $procedure = $parser->createProcedure();
        $this->getApplication()->add($procedure);
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

    public function testParsedProcedureShouldDisplayErrorsForMissingPlugins()
    {
        $this->expectExceptionMessage("[FATAL] Missing required plugin 'project'.");
        $this->registerProcedure('php-package.jin');
        $tester = $this->runProcedure('php-package');
    }

    public function testParsedProcedureShouldDisplayErrorsForMissingArguments()
    {
        $this->expectExceptionMessage('[core:shell ]'.PHP_EOL."[FATAL] Missing required argument 'shell_command'");
        $this->registerProcedure('invalid-arguments.jin');
        $tester = $this->runProcedure('invalid-arguments');
    }

    public function testParsedProcedureShouldDisplayErrorsForNonexistentOptions()
    {
        $this->registerProcedure('invalid-options.jin');
        $tester = $this->runProcedure('invalid-options');
    }
}
