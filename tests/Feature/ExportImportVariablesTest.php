<?php

namespace Tests\Feature;

/* use PHPunit\Framework\TestCase; */
use Nonetallt\Jinitialize\Testing\TestCase;

use Tests\Traits\Paths;
use Tests\Classes\TestApplication;

use Tests\Classes\TestExportCommand;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\JinitializeContainer;
use Nonetallt\Jinitialize\Plugin;
use Tests\Classes\TestImportCommand;

use PHPunit\Framework\Constraint;

class ExportImportVariablesTest extends TestCase
{
    use Paths;

    public function testExportVariablesFromContainer()
    {
        $this->runCommand(TestExportCommand::class);
        $this->assertContainerEquals(['variable1' => 1, 'variable2' => 2], 'test');
    }

    public function testImportVariablesFromContainer()
    {
        $this->runCommand(TestExportCommand::class);
        $this->runCommand(TestImportCommand::class);

        $this->assertContainerEquals(['variable1' => 1, 'variable2' => 2, 'variable3' => '12'], 'test');
        $this->assertContainerContains(['variable3' => '12'], 'test');
    }
}
