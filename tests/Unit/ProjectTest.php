<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;
use Nonetallt\Jinitialize\JinitializeApplication;
use Nonetallt\Jinitialize\PluginContainer;

use Tests\Traits\CleansOutput;

class ProjectTest extends TestCase
{
    use CleansOutput;

    private $project;
    private $stubsFolder;
    private $libraryRoot;

    public function testCreatePluginComposerStub()
    {
        /* $container = new PluginContainer('test'); */
        /* $container->setArray([ */
        /*     'authorName'        => 'Jyri Mikkola', */
        /*     'authorEmail'       => 'jyri.mikkola@pp.inet.fi', */
        /*     'nickname'          => 'nonetallt', */
        /*     'pluginName'        => 'nonetallt/jinitialize-plugin-test', */
        /*     'pluginNamespace'   => 'Nonetallt\\\\Jinitialize\\\\Plugin\\\\Test\\\\', */
        /*     'pluginDescription' => 'This is a test' */
        /* ]); */

        /* $this->project->copyStubsFrom($this->stubsFolder, $container->exportData()); */
        /* $expected = $this->libraryRoot . '/tests/expected/composer.json'; */
        /* $output = $this->project->getPath().'/composer.json'; */
        /* $this->assertEquals(file_get_contents($expected), file_get_contents($output)); */
    }

    public function testCreateStructure()
    {
        $this->project->createStructure([
            'level1' => [
                'level2' => [
                    'level3'
                ]
            ]
        ]);

        $str = str_replace('|', PHP_EOL, 'project|--level1|----level2|------level3|');
        /* var_dump($str); */
        /* var_dump(( string )$this->project->getFolders()); */
        $this->assertEquals($str, (string)$this->project->getFolders());
    }

    /**
     * Remove files generated from stubs from the output folder
     */
    public function setUp()
    {
        $this->cleanOutput();

        /* Create project in the output directory */
        $this->project = new Project($this->outputFolder() . '/project');
        $this->libraryRoot = dirname(dirname(__DIR__));
        $this->stubsFolder = $this->libraryRoot . '/stubs/plugin';
    }

    
}
