<?php

namespace Nonetallt\Jinitialize;

use Nonetallt\Jinitialize\Plugin;

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
