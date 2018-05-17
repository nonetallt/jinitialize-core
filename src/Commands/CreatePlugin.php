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




        /* Create dest folder */
        $dest = "$baseDir/$pluginName";
        if(file_exists($dest)) {
            $this->abort("Project directory already exists: $dest");
        }
        if(! mkdir($dest, 0755)) {
            $this->abort("Could not create project folder at: $dest");
        }




        /* Copy files */
        $files = array_diff(scandir($stubDir), ['.', '..']);

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

        foreach($files as $file) {
            $stub = new StubGenerator("$stubDir/$file", "$dest/$file");
            $test = $stub->render([
                '[PLUGIN_NAME]'        => $composerName,
                '[PLUGIN_DESCRIPTION]' => $description,
                '[AUTHOR_NAME]'        => $author,
                '[AUTHOR_EMAIL]'       => $email,
                '[PLUGIN_NAMESPACE]'   => $namespace,
            ]);
        }

        /* Create folders */
        $structure = [
            'src' => [],
            'tests' => ['Unit', 'Feature', 'output', 'Traits'],
        ];
        $this->createStructureIn($structure, $dest);
    }

    public function createComposerFile()
    {

    }

    private function createStructureIn(array $structure, string $to)
    {
        foreach($structure as $folder => $subfolders) {

            if(empty($subfolders)) {
                mkdir("$to/$folder");
            }
            foreach($subfolders as $subfolder) {
                mkdir("$to/$folder/$subfolder", 0755, true);
            }
        }
    }

    public function revert()
    {

    }

    public function recommendsRoot()
    {
        return true;
    }
}
