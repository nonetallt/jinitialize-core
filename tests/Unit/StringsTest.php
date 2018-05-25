<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Strings;
use Nonetallt\Jinitialize\PluginContainer;

class StringsTest extends TestCase
{
    public function testCutAfter()
    {
        $subject = 'src/folder1/folder2';
        $result = Strings::cutAfter($subject, '/');
        $this->assertEquals('src', $result);
    }

    public function testCutUntilExclude()
    {
        $subject = 'src/folder1/folder2';
        $result = Strings::cutUntil($subject, '/', false);
        $this->assertEquals('folder1/folder2', $result);
    }

    public function testCutUntilInclude()
    {
        $subject = 'src/folder1/folder2';
        $result = Strings::cutUntil($subject, '/', true);
        $this->assertEquals('/folder1/folder2', $result);
    }

    public function testReplaceFirst()
    {
        $result = Strings::replaceFirst('t', '', 'testi');
        $this->assertEquals('esti', $result);
    }

    public function testReplaceFirstMultipleCharacters()
    {
        $result = Strings::replaceFirst('abc', '', 'abc1 abc2 abc3');
        $this->assertEquals('1 abc2 abc3', $result);
    }

    public function testReplaceFirstReplacement()
    {
        $result = Strings::replaceFirst('abc', 'asd', 'abc1 abc2 abc3');
        $this->assertEquals('asd1 abc2 abc3', $result);
    }

    public function testAfterLast()
    {
        $result = Strings::afterLast('App/Domain/Helpers/Traits', '/');
        $this->assertEquals('Traits', $result);
    }

    public function testUntilLast()
    {
        $result = Strings::untilLast('App/Domain/Helpers/Traits', '/');
        $this->assertEquals('App/Domain/Helpers', $result);
    }

    public function testPackageNamespace()
    {
        $result = Strings::packageNamespace('nonetallt', 'jinitialize-plugin-composer');
        $this->assertEquals('Nonetallt\\\\Jinitialize\\\\Plugin\\\\Composer\\\\', $result);
    }

    public function testCamelbackCase()
    {
        $result = Strings::toSnakeCase('testVariable');
        $this->assertEquals('test_variable', $result);
    }

    public function testConvertToStubCase()
    {
        $result = PluginContainer::transformForStub('testVariable');
        $this->assertEquals('[TEST_VARIABLE]', $result);
    }
        
}
