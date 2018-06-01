<?php

namespace Tests\Feature;

use Tests\Classes\TestInputCommand;
use Nonetallt\Jinitialize\JinitializeContainer;
use Nonetallt\Jinitialize\Testing\TestCase;

class JinitializeContainerTest extends TestCase
{
    
    public function testGetDataEmpty()
    {
        $this->assertEmpty($this->container()->getData('test'));
    }

    public function testGetDataValues()
    {
        $this->container()->getPlugin('test')->getContainer()->set('value', 1); 
        $this->assertEquals([
            'test' =>  ['value' => 1]
        ], 
        $this->container()->getData());
    }

    public function testGetDataScope()
    {
        $this->container()->getPlugin('test')->getContainer()->set('value', 1); 
        $this->container()->addPlugin('test2');
        $this->container()->getPlugin('test2')->getContainer()->set('value', 2);

        $this->assertEquals(['value' => 1], $this->container()->getData('test'));
    }

    private function container()
    {
        return $this->getApplication()->getContainer();
    }
}
