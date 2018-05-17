<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Installer;
use Dotenv\Dotenv;
use SebastiaanLuca\StubGenerator\StubGenerator;

class GenerateStubTest extends TestCase
{
    public function testApache2ConfStub()
    {
        $dotenv = new Dotenv(__DIR__ . '/../..');
        $dotenv->load();

        $site = env('SITE_NAME');

        $input = __DIR__ . '/../../stubs/apache2-site.stub';
        $output = __DIR__ . "/../output/$site.out";
        $expected = __DIR__ . '/../expected/apache2-site.conf';

        $stub = new StubGenerator($input, $output);
        $test = $stub->render([
            '[APP_NAME]'  => env('APP_NAME'),
            '[APP_PATH]'  => env('APP_PATH'),
            '[SITE_NAME]' => env('SITE_NAME'),
        ]);

        $this->assertEquals(file_get_contents($output), file_get_contents($expected));
    }

    public function testCreatePluginComposerStub()
    {
        $input = __DIR__ . '/../../stubs/plugin/composer.json';
        $output = __DIR__ . "/../output/composer.out";
        $expected = __DIR__ . '/../expected/plugin/composer.json';
        $this->assertEquals(file_get_contents($output), file_get_contents($expected));
    }

    /**
     * Remove files generated from stubs from the output folder
     */
    public function setUp()
    {
        $folder = __DIR__ . '/../output';

        /* Get all filenames with .out extension */
        $files = glob("$folder/*.out");

        foreach($files as $file) {
            unlink($file);
        }
    }
}
