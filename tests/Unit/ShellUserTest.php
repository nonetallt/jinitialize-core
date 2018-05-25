<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\ShellUser;

class ShellUserTest extends TestCase
{
    function testInitializeClass()
    {
        $user = ShellUser::getInstance();
        $this->assertInstanceOf(ShellUser::class, $user);
    }
}
