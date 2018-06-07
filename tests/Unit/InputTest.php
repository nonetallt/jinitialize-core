<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

use Nonetallt\Jinitialize\Input\Input;

class InputTest extends TestCase
{
    private $input;
    private $values;

    public function setUp()
    {
        parent::setUp();

        $this->values = [
            'arg1'   => 'value1',
            'arg2'   => '[ENV]',
            'arg3'   => 'value3',
            '--opt1' => '[test:placeholder]',
        ];

        $input = new ArrayInput($this->values);
        $input->bind(new InputDefinition([
           new InputArgument('arg1', InputArgument::REQUIRED),
           new InputArgument('arg2', InputArgument::REQUIRED),
           new InputArgument('arg3', InputArgument::REQUIRED),
           new InputOption('opt1', null, InputOption::VALUE_REQUIRED)
        ]));

        $this->input = new Input($input);
    }

    public function testClassCanBeInitialized()
    {
        $this->assertInstanceOf(Input::class, $this->input);
    }

    public function testValuesReturnsAllArgumentsAndOptions()
    {
        $this->assertEquals([
            'arg1'   => 'value1',
            'arg2'   => '[ENV]',
            'arg3'   => 'value3',
            'opt1' => '[test:placeholder]',
        ], 
        $this->input->values());
    }

    public function testEnvPlaceholdersAreReplaced()
    {
        $_ENV['ENV'] = 'value2';

        $this->input->replaceEnvPlaceholders();
        $this->assertEquals([
            'arg1'   => 'value1',
            'arg2'   => 'value2',
            'arg3'   => 'value3',
            'opt1' => '[test:placeholder]',
        ], $this->input->values());
    }

    public function testExportedPlaceholdersAreReplaced()
    {
        $this->getApplication()
            ->getContainer()
            ->getPlugin('test')
            ->getContainer()
            ->set('placeholder', 'value4');
            
        $this->input->replaceExportedPlaceholders();
        $this->assertEquals([
            'arg1'   => 'value1',
            'arg2'   => '[ENV]',
            'arg3'   => 'value3',
            'opt1' => 'value4',
        ], 
        $this->input->values());
    }

    public function testSetFormatThrowsExceptionWhenDollarSymbolIsMissing()
    {
        $this->expectExceptionMessage('Format [] must contain placeholder name denoted by $.');
        $this->input->setFormat('[]');
    }

    public function testGetEnvPlaceholdersReturnsAllEnvPlaceholders()
    {
        $this->assertEquals(['ENV'], $this->input->getEnvPlaceholders());
    }

    public function testGetExportedPlaceholdersReturnsAllExportedPlaceholders()
    {
        $this->assertEquals(['test:placeholder'], $this->input->getExportedPlaceholders());
    }

    public function testGetPlaceholdersReturnsAllPlaceholderStrings()
    {
        $this->assertEquals([
            'ENV',
            'test:placeholder'
        ], 
        $this->input->getPlaceholders());
    }

    public function testMissingEnvReturnsEnvWhenNoEnvValueIsDefined()
    {
        /* Make sure env does not have ENV key */
        unset($_ENV['ENV']);

        $this->assertEquals(['ENV'], $this->input->missingEnv());
    }

    public function testMissingEnvIsEmptyWhenEnvValueIsDefined()
    {
        $_ENV['ENV'] = 'value2';

        $this->assertEmpty($this->input->missingEnv());
    }
}
