<?php

namespace Nonetallt\Jinitialize;

class JinitializeContainer
{
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function createPlugin(string $name)
    {
        $this->data[$name] = new JinitializePluginContainer($name);
        return $this->data;
    }

    public function getPlugin(string $name)
    {
        if(! isset($this->data[$name])) throw new \Exception("Plugin $name not found");
        return $this->data[$name];
    }

    /* public function get(string $key, string $plugin = null) */
    /* { */
    /*     if(is_null($plugin)) { */
    /*         if(is_null($this->plugin)) throw new \Exception('Something went wrong'); */
    /*         $plugin = $this->plugin; */
    /*     } */

    /*     if(! isset($this->data[$plugin])) { */
    /*         throw new \Exception("Can't get data from '$plugin' : plugin not found."); */
    /*     } */

    /*     $pluginContainer = $this->data[$plugin]; */

    /*     if(! isset($pluginContainer[$key])) { */
    /*         throw new \Exception("Key '$key' is not set for plugin '$plugin'"); */
    /*     } */

    /*     return $pluginContainer[$key]; */
    /* } */
}
