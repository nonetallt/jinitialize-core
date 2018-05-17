<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Validation\Jvalidator;

class ValidateMySqlDatabaseNameTest extends TestCase
{
    function testValidName()
    {
        $this->assertTrue(Jvalidator::mySqlDatabaseName('testi'));
    }

    function testZeroCharacters()
    {
        $this->assertFalse(Jvalidator::mySqlDatabaseName(''));
    }

    function testForwardSlash()
    {
        $this->assertFalse(Jvalidator::mySqlDatabaseName('testi/'));
    }

    function testBackwardSlash()
    {
        $this->assertFalse(Jvalidator::mySqlDatabaseName('tes\ti'));
    }

    function testEndingInWhitespace()
    {
        $this->assertFalse(Jvalidator::mySqlDatabaseName('testi '));
    }

    public static function setUpBeforeClass()
    {
    }
}
