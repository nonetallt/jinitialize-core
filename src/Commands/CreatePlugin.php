<?php

namespace Nonetallt\Jinitialize\Commands;

use Nonetallt\Jinitialize\Plugin\JinitializeCommand as Command;
use SebastiaanLuca\StubGenerator\StubGenerator;

class CreatePlugin extends Command
{

    protected function configure()
    {
        $this->setName('create:plugin')->setDescription('Create a new jinitialize plugin project');
    }

    protected function handle()
    {
        $io = $this->getIo();

        $pluginName = 'jinitialize-plugin-' . $io->ask('Give a name for the plugin (jinitialize-plugin-)');

        $projectDir = dirname(__DIR__, 2);

        /* Package dir path TODO ask location, use env */
        $baseDir = dirname(__DIR__, 3);
        $stubDir = "$projectDir/stubs/plugin";


        $project = new Project("$baseDir/$pluginName");
        
        if(! $project->isPathValid()) {
            $this->abort("Project directory already exists: $dest");
        }
        if(! $project->createFolder()) {
            $this->abort("Could not create project folder at: $dest");
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


        $authorNick = 'nonetallt';
        $author = 'Jyri Mikkola';
        $email = 'jyri.mikkola@pp.inet.fi';
        $composerName = "$authorNick/$pluginName";

        $pluginParts = explode('-', $pluginName);
        array_walk($pluginParts, function($part) {
            return ucfirst($part) . '\\\\';
        });

        $namespace = ucfirst($authorNick) . '\\\\' . implode('', $pluginParts);
        /* $namespace = ucfirst($authorNick) . '\\\\' . ucfirst($pluginName) . '\\\\'; */

        $description = $io->ask('Give a package description');

        /* foreach($files as $file) { */
        /*     $stub = new StubGenerator("$stubDir/$file", "$dest/$file"); */
        /*     $test = $stub->render([ */
        /*         '[PLUGIN_NAME]'        => $composerName, */
        /*         '[PLUGIN_DESCRIPTION]' => $description, */
        /*         '[AUTHOR_NAME]'        => $author, */
        /*         '[AUTHOR_EMAIL]'       => $email, */
        /*         '[PLUGIN_NAMESPACE]'   => $namespace, */
        /*     ]); */
        /* } */

        
    }

    /* TODO placegolder */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function createComposerFile(string $name, string $description, string $author, string $email, string $namespace)
    {
        $stub = new StubGenerator("$stubDir/$file", "$dest/$file");
        $test = $stub->render([
            '[PLUGIN_NAME]'        => $name,
            '[PLUGIN_DESCRIPTION]' => $description,
            '[AUTHOR_NAME]'        => $author,
            '[AUTHOR_EMAIL]'       => $email,
            '[PLUGIN_NAMESPACE]'   => $namespace,
        ]);
    }

    
    public function revert()
    {

    }

    public function recommendsRoot()
    {
        return true;
    }
}
