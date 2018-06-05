<?php

namespace Nonetallt\Jinitialize;

use Symfony\Component\Console\Application;
use Dotenv\Dotenv;
use Nonetallt\Jinitialize\Commands\CreatePlugin;
use Nonetallt\Jinitialize\Procedure;
use Nonetallt\Jinitialize\JinitializeCommand;
use Nonetallt\Jinitialize\Plugin;

/**
 * Responsible for registering the application commands and procedures
 *
 */
class JinitializeApplication extends Application
{
    public function __construct(string $dotenvDir = null)
    {
        parent::__construct('jinitialize', '1.0.0');

        if(! is_null($dotenvDir) && file_exists($dotenvDir . '/.env')) {
            /* Load .env when application is created */
            $dotenv = new Dotenv($dotenvDir);
            $dotenv->load();
        }
    }

    public function registerApplicationCommands()
    {
        if(! $this->getContainer()->hasPlugin('core')) {
            $this->getContainer()->addPlugin('core');
        }

        $command = new CreatePlugin('core');

        if(! $this->has($command->getName())) {
            $this->add($command);
        }
    }

    /**
     * @param string $pluginsManifest path to plugins.php file
     */
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
    public function registerPlugin(array $plugin)
    {
        /* Dont't process nameless plugins */
        if(! isset($plugin['name'])) return;

        /* Create a container for the plugin */
        $this->getContainer()->addPlugin($plugin['name']);

        /* Commands are registered first so they can be found be porcedures */
        if(isset($plugin['commands'])) {
            $this->registerCommands($plugin['name'], $plugin['commands']);
        }

        if(isset($plugin['procedures'])) {
            /* The path info is appended by ComposerScripts */
            $this->registerProcedures($plugin['name'], $plugin['procedures'], $plugin['path']);
        }

        if(isset($plugin['settings'])) {
            $this->registerSettings($plugin['name'], $plugin[ 'settings' ]);
        }
    }

    /**
     * Register all procedures for a given plugin
     */
    public function registerProcedures(string $plugin, array $procedures, string $path)
    {
        /* Append the installation path before the files */
        array_walk($procedures, function(&$procedure) use ($path){
            $procedure = "$path/$procedure";
        });

        /* Create a factory from list of paths defined by plugin */
        $factory = new ProcedureFactory($this, $procedures);

        /* Get a list of all procedure names contained in file paths */ 
        foreach($factory->getNames() as $name) {
            /* Use the factory to create and register each procedure */
            $procedure = $factory->create($name);
            $this->add($procedure);
        }
    }

    /**
     * Register all commands for a given plugin
     *
     * @param string $plugin The plugin these commands will be registered for
     * @param array $commands array containing the classnames of the commands
     *
     * @return array $newCommands array containing JinitializeCommand objects
     *
     */
    public function registerCommands(string $plugin, array $commands)
    {
        $newCommands = [];

        foreach($commands as $commandClass) {
            $command = new $commandClass($plugin);
            $command = CommandFactory::setNamespace($command);
            $this->add($command);
            $newCommands[] = $command;
        }
        return $newCommands;
    }

    public function registerSettings(string $plugin, array $settings)
    {
        $this->getContainer()->getPlugin($plugin)->setSettings($settings);
    }

    public function getContainer()
    {
        return JinitializeContainer::getInstance();
    }

    public function recommendedSettings()
    {
        $settings = [];
        foreach($this->getContainer()->getPlugins() as $plugin) {
            $settings[$plugin->getName()] = $plugin->getSettings();
        }
        return $settings;
    }

    /**
     * Get an array of plugins and their settings that are not defined in env
     *
     */
    public function missingSettings()
    {
        $missing = [];
        foreach($this->recommendedSettings() as $plugin => $settings) {
            foreach($settings as $setting) {
                /* Get value for the setting */
                $value = $_ENV[$setting] ?? null;

                if(is_null($value)) $missing[$plugin][] = $setting;
            }
        }
        return $missing;
    }

    public function getNew($name)
    {
        $command =  parent::get($name);
        return $this->newCommand($command);
    }

    private function newCommand($from)
    {
        if(! is_subclass_of($from, JinitializeCommand::class)) return $from;

        $class = get_class($from);
        $command = new $class($from->getPluginName());

        return $command;
    }
}
