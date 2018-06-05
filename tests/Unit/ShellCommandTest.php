<?php

namespace Tests\Unit;

use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Commands\ShellCommand;

use Tests\Traits\Paths;

class ShellCommandTest extends TestCase
{
    use Paths;

    public function testCanRunMultiArgumentShellCommandAndFindsComposerInLsOutput()
    {
        $this->runCommand('core:shell "ls -la" output');
        $output = $this->getApplication()->getContainer()->getData()['core']['output'];
        $this->assertContains('composer.json', $output);
    }

    public function testExportsOutputAsExportArgument()
    {
        $tester = $this->runCommand('core:shell "ls " output');
        $this->assertContainerContains(['output' => $tester->getDisplay()]);
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin($this->projectRoot().'/composer.json');
    }
}
