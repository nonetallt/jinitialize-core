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

    public function addPlugin(string $plugin)
    {
        $this->plugins[$plugin] = new Plugin($plugin);

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
