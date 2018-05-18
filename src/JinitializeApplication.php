<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Application;
use Dotenv\Dotenv;
use Nonetallt\Jinitialize\Commands\CreatePlugin;
use Nonetallt\Jinitialize\Procedure;

class JinitializeApplication extends Application
{
    private $plugins;
    private $container;

    public function __construct(string $dotenvDir)
    {
        parent::__construct();
        $this->container = new JinitializeContainer();
        $this->plugins = [];

        /* Load .env when application is created */
        $dotenv = new Dotenv($dotenvDir);
        $dotenv->load();
    }

    public function registerPlugins(string $packagesFile)
    {
        $packages = [];
        $this->pluginsFile = $packagesFile;

        if(file_exists($packagesFile)) {
            /* Return the var_export */
            $packages = include $packagesFile;
        }

        foreach($packages as $package) {
            if(!empty($package['plugins'])) {
                foreach($package['plugins'] as $plugin) {
                    $this->plugins = $plugin;
                }
            }
        }

        $this->registerCommands();
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    private function registerCommands()
    {
        foreach($this->plugins as $plugin) {

            $this->container->createPlugin($plugin->getName());

            foreach($plugin->commands() as $commandClass) {
                $command = new $commandClass();
                $this->add($command);
            }
        }

        /* TODO Create procedure out of every single command */ 
        $command = new CreatePlugin();
        $procedure = new Procedure($command->getName(), $command->getDescription(), [$command]);
        $procedure->setContainer($this->container);
        $this->add($procedure);
    }
}
