<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Nonetallt\Jinitialize\Installer;
use Dotenv\Dotenv;
use SebastiaanLuca\StubGenerator\StubGenerator;

use Tests\Traits\CleansOutput; 

class GenerateStubTest extends TestCase
{
    use CleansOutput;

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
}
