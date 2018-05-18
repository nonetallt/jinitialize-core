<?php

namespace Nonetallt\Jinitialize\Commands;

use Nonetallt\Jinitialize\Plugin\JinitializeCommand as Command;

class CreatePlugin extends Command
{

    protected function configure()
    {
        $this->setName('create:plugin')->setDescription('Create a new jinitialize plugin project');
    }

    protected function handle()
    {
        $io = $this->getIo();

        /* Ask for variables when not testing */
        if(env('APP_ENV') !== 'testing') {
            $pluginName = 'jinitialize-plugin-' . $io->ask('Give a name for the plugin (jinitialize-plugin-)');
            $description = $io->ask('Give a package description');
        }


        /* Package dir path TODO ask location, use env */
        $baseDir    = dirname(__DIR__, 3);
        $projectDir = dirname(__DIR__, 2);
        $stubDir    = "$projectDir/stubs/plugin";


        $project = new Project("$baseDir/$pluginName");
        
        if(! $project->isPathValid()) {
            $this->abort("Project directory already exists or is not writable: $dest");
        }
        
        $project->createStructure([
            'src' => [],
            'tests' => [
                'Unit', 
                'Feature', 
                'output', 
                'Traits'
            ]
        ]);

        $project->copyFilesFrom($stubDir);
        $project->copyStubsFrom($stubDir, [
            '[PLUGIN_NAME]'        => $composerName,
            '[PLUGIN_DESCRIPTION]' => $description,
            '[AUTHOR_NAME]'        => $author,
            '[AUTHOR_EMAIL]'       => $email,
            '[PLUGIN_NAMESPACE]'   => $namespace,
        ]);


        $authorNick = 'nonetallt';
        $author = 'Jyri Mikkola';
        $email = 'jyri.mikkola@pp.inet.fi';
        $composerName = "$authorNick/$pluginName";

        
    }

    public function recommendsDefaults()
    {
        return [
            'packages directory',
            'author name',
            'author nickname',
            'author email'
        ];
    }

    public function importsVariables()
    {

    }

    public function exportsVariables()
    {

    }

    public function revert()
    {

    }

    public function recommendsRoot()
    {
        return true;
    }
}
