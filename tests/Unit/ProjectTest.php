<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Helpers\Project;

class ProjectTest extends TestCase
{

    public function testCreatePluginComposerStub()
    {
        /* Create project in the output directory */
        $project = new Project(__DIR__ . '/../output');

        $input = __DIR__ . '/../../stubs/plugin';
        $expected = __DIR__ . '/../expected/plugin/composer.json';

        $project->copyStubsFrom($input, []);


        $command = new \Nonetallt\Jinitialize\Commands\CreatePlugin();
        $command->author = 'Jyri Mikkola';
        $command->email = 'jyri.mikkola@pp.inet.fi';
        $command->nickname = 'nonetallt';
        $command->packageName = 'nonetallt/jinitialize-plugin-test';
        /* $command->namespace */ 

        $this->assertEquals(file_get_contents($project->getPath().'/composer.json'), file_get_contents($expected));
    }

    /**
     * Remove files generated from stubs from the output folder
     */
    public function setUp()
    {
        $folder = __DIR__ . '/../output';

        /* Get all filenames with .out extension */
        $files = glob("$folder/*");

        foreach($files as $file) {
            unlink($file);
        }
    }
}
