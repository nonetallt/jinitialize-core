<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Tests\Traits\Paths;

class AskCommandTest extends TestCase
{
    use Paths;

    public function testCommandImportsTheAskedValueIntoContainer()
    {
        $input = ['input'];
        $this->runCommand('core:ask key', [], $input);
        $this->assertContainerContains(['key' => 'input']);
    }

    public function testCommandPrependsPrependOptionBeforeIinput()
    {
        $input = ['input'];
        $this->runCommand('core:ask key --prepend=123', [], $input);
        $this->assertContainerContains(['key' => '123input']);
    }

    public function testCommandAppendsAppendOptionAfterInput()
    {
        $input = ['input'];
        $this->runCommand('core:ask key --append=123', [], $input);
        $this->assertContainerContains(['key' => 'input123']);
    }

    public function testCommandShowsDefaultMessageWhenAskingForValue()
    {
        $input = ['input'];
        $tester = $this->runCommand('core:ask key', [], $input);
        $this->assertContains("Give a value for key 'key'", $tester->getDisplay());
    }

    public function testCommandShowsCustomMessageWhenMessageOptionIsUsed()
    {
        $input = ['input'];
        $tester = $this->runCommand('core:ask key --message="Enter password"', [], $input);
        $this->assertContains('Enter password', $tester->getDisplay());
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin($this->projectRoot().'/composer.json');
    }
}
