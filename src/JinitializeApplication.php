<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Application;
use Dotenv\Dotenv;
use Nonetallt\Jinitialize\Commands\CreatePlugin;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\Plugin\JinitializeCommand;
use Nonetallt\Jinitialize\Plugin\Plugin;

/**
 * Responsible for registering the application commands and procedures
 *
 */
class JinitializeApplication extends Application
{
    public function __construct(string $dotenvDir)
    {
        parent::__construct();

        /* Load .env when application is created */
        $dotenv = new Dotenv($dotenvDir);
        $dotenv->load();
    }

    public function registerApplicationCommands()
    {
        $this->add(new CreatePlugin('core'));
    }

    public function registerPlugins(string $pluginsManifest)
    {
        $packages = [];
        $plugins = ComposerScripts::loadPluginsManifest($pluginsManifest);

        foreach($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * Register a plugin found in manifest file
     *
     * @param array $plugin
     * @return null
     */
    private function registerPlugin(array $plugin)
    {
        /* Dont't process nameless plugins */
        if(! isset($plugin['name'])) return;

        /* Create a container for the plugin */
        JinitializeContainer::getInstance()->addPlugin($plugin['name']);

        if(isset($plugin['commands'])) {
            $this->registerCommands($plugin['name'], $plugin['commands']);
        }

        if(isset($plugin['procedures'])) {
            $this->registerProcedures($plugin['name'], $plugin['procedures']);
        }
    }

    private function registerProcedures(string $plugin, array $procedures)
    {
        /* TODO */
        /* procedure factory */
    }

    /**
     * Register all commands for a given plugin
     */
    private function registerCommands(string $plugin, array $commands)
    {
        foreach($commands as $commandClass) {
            $command = new $commandClass($plugin);
            $this->add($command);
        }
    }
}
