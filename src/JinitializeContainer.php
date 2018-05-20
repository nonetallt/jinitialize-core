<?php

namespace Nonetallt\Jinitialize;

use Nonetallt\Jinitialize\Plugin\Plugin;

class JinitializeContainer
{
    private $plugins;

    public function __construct()
    {
        $this->plugins = [];
    }

    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;

        return $this->plugins;
    }

    public function getPlugin(string $name)
    {
        if(! isset($this->plugins[$name])) throw new \Exception("Plugin $name not found");
        return $this->plugins[$name];
    }

    public function getPlugins()
    {
        return $this->plugins;
    }
}
