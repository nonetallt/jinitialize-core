<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Installer;

class RunCommandTest extends TestCase
{
    public function testTest()
    {
        $installer = new Installer();
        $result = $installer->boot();

        $this->assertTrue($result);
    }
}
