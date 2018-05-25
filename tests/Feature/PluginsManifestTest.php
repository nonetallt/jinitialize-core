<?php

namespace Tests\Feature;

use PHPunit\Framework\TestCase;
use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\ComposerScripts;
use Tests\Classes\TestImportCommand;
use Tests\Classes\TestExportCommand;


class PluginsManifestTest extends TestCase
{
    use CleansOutput;

    public function testGenerateManifest()
    {
        /* Create plugins.php file as manifest output */
        $output = $this->outputFolder() . '/plugins.php';

        $data = [[
                'extra' => [
                    'jinitialize-plugin' => [
                        'name' => 'test',
                        'commands' => [
                            TestImportCommand::class,
                            TestExportCommand::class,
                        ]
                    ]
                ]
            ]];

        ComposerScripts::generatePluginsManifest($data, $output);

        var_dump(file_get_contents($output));

        $result = include $output;
        var_dump($result);
        /* $this->assertTrue(include $output); */
    }
}
