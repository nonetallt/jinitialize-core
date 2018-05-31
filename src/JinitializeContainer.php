<?php

namespace Nonetallt\Jinitialize;

class JinitializeContainer
{
    private $plugins;
    private static $instance;

    private function __construct()
    {
        $this->plugins = [];
    }

    public static function getInstance()
    {
        if(is_null( self::$instance )) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Used for testing
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    public function addPlugin(string $plugin)
    {
        if(isset($this->plugins[$plugin])) {
            throw new \Exception("Plugin $plugin is already registered!");
        }

        $this->plugins[$plugin] = new Plugin($plugin);
        return $this->plugins;
    }

    public function hasPlugin(string $name)
    {
        return isset($this->plugins[$name]);
    }

    public function getPlugin(string $name)
    {
        if(! $this->hasPlugin($name)) throw new \Exception("Plugin $name not found");
        return $this->plugins[$name];
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Get data in the container.
     *
     * @param string|null $plugin plugin scope to get only data for a specific
     * plugin.
     *
     */
    public function getData(string $plugin = null)
    {
        /* Only get data for single plugin */
        if(! is_null($plugin)) {
            return $this->getPlugin($plugin)->getContainer()->getData();
        }

        /* Get data for all plugins */
        $data = [];
        foreach($this->plugins as $plugin) {
            $data[$plugin->getName()] = $plugin->getContainer()->getData();
        }
        return $data;
    }

    public function __toString()
    {
        return print_r($this->getData(), true);
    }
}
