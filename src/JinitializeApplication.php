<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Application;
use Dotenv\Dotenv;
use Nonetallt\Jinitialize\Commands\CreatePlugin;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Nonetallt\Jinitialize\Plugin\Plugin;

class JinitializeApplication extends Application
{
    private $container;

    public function __construct(string $dotenvDir)
    {
        parent::__construct();
        $this->container = new JinitializeContainer();

        /* Load .env when application is created */
        $dotenv = new Dotenv($dotenvDir);
        $dotenv->load();
    }

    public function registerPlugins(string $packagesFile)
    {
        $packages = [];

        if(file_exists($packagesFile)) {
            /* Return the var_export */
            $packages = include $packagesFile;
        }

        foreach($packages as $package) {
            if(!empty($package['plugins'])) {
                foreach($package['plugins'] as $plugin) {
                    $this->registerPlugin($plugin);
                }
            }
        }
    }

    public function registerPlugin(Plugin $plugin)
    {
        $this->container->addPlugin($plugin);
        $this->registerCommands($plugin);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function registerProcedures()
    {

    }

    /**
     * Register all commands for a given plugin
     */
    private function registerCommands(Plugin $plugin)
    {
        foreach($plugin->commands() as $commandClass) {
            $command = new $commandClass($plugin);
            $this->add($command);
        }

        /* TODO move? application commands */
        /* $this->add(new CreatePlugin($plugin)); */
    }

    public function registerProcedure(Procedure $procedure, Plugin $plugin = null)
    {
        /* Register procedure */
        $this->add($procedure);

        /* Register every command used by procedure so they can be run by procedure */
        foreach($procedure->getCommands() as $command) {
            $this->registerCommand($command, $plugin);
        }

        /* Add container managed by application to the procedure */
        $procedure->setContainer($this->container);

        return $procedure;
    }
}
