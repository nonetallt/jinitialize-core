<?php

namespace Tests\Unit;

use PHPunit\Framework\TestCase;

use Nonetallt\Jinitialize\Helpers\Folder;

class FolderTest extends TestCase
{
    private $folder;

    public function testGetSubfolder()
    {
        $this->assertEquals('tests', $this->folder->getSubfolder('tests')->getName());
    }

    public function testGetSubfolders()
    {
        $this->assertCount(2, $this->folder->getSubfolders());
    }

    public function testGetPath()
    {
        $this->assertCount(1, $this->folder->getPath('src/recursive1/recursive2')->getSubfolders());
    }

    public function setUp()
    {
        $structure = [
            'src' => [
                'recursive1' => [
                    'recursive2' => [
                        'folder3'
                    ],
                    'folder2'
                ]
            ],
            'tests' => [
                'Unit', 
                'Feature', 
                'output', 
                'Traits'
            ]
        ];
        $this->folder = new Folder('test', $structure); 
    }
        
}
