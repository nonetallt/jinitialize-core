<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;
use Nonetallt\Jinitialize\JinitializeApplication;

class ProjectTest extends TestCase
{
    private $project;
    private $stubsFolder;
    private $libraryRoot;

    public function testCreatePluginComposerStub()
    {
        $app = new JinitializeApplication($this->libraryRoot);
        $app->registerPlugins($this->libraryRoot.'/boostrap/cache/plugins.php');

        $container = $app->getContainer();
        $container->createPlugin('test');

        $plugin = $container->getPlugin('test');
        $plugin->exportVariables([
            'authorName'        => 'Jyri Mikkola',
            'authorEmail'       => 'jyri.mikkola@pp.inet.fi',
            'nickname'          => 'nonetallt',
            'pluginName'        => 'nonetallt/jinitialize-plugin-test',
            'pluginNamespace'   => 'Nonetallt\\\\Jinitialize\\\\Plugin\\\\Test\\\\',
            'pluginDescription' => 'This is a test'
        ]);

        $this->project->copyStubsFrom($this->stubsFolder, $plugin->exportData());

        /* $command = new \Nonetallt\Jinitialize\Commands\CreatePlugin(); */
        
        $expected = $this->libraryRoot . '/tests/expected/composer.json';
        $output = $this->project->getPath().'/composer.json';
        $this->assertEquals(file_get_contents($expected), file_get_contents($output));
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
        $folder = __DIR__ . '/../output';
        $this->removeDirectoryContents($folder);

        /* Create project in the output directory */
        $this->project = new Project($folder . '/project');
        $this->libraryRoot = dirname(dirname(__DIR__));
        $this->stubsFolder = $this->libraryRoot . '/stubs/plugin';
    }

    private function removeDirectoryContents(string $dir, int $level = 1)
    {
        if(! is_dir($dir)) return;
        
        $objects = scandir($dir); 

        foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (is_dir($dir."/".$object)) {
                    $this->removeDirectoryContents($dir."/".$object, $level+1);
                }
                else {
                    unlink($dir."/".$object); 
                }
            } 
        }
        if($level > 1) rmdir($dir); 
    }
}
