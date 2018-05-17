<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Application;
use Dotenv\Dotenv;

class JinitializeApplication extends Application
{
    private $plugins;
    private $container;

    public function __construct(string $dotenvDir)
    {
        parent::__construct();
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

    public function getPlugins()
    {
        return $this->plugins;
    }

    private function registerCommands()
    {
        foreach($this->plugins as $plugin) {
            foreach($plugin->commands() as $commandClass) {
                $command = new $commandClass();
                /* TODO */
                $command->setContainer($this->container);
                $this->add($command);
            }
        }
        $this->add(new \Nonetallt\Jinitialize\Commands\CreatePlugin());
    }
}
