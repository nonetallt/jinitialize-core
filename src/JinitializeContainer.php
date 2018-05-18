<?php

namespace Nonetallt\Jinitialize;

class JintiializeContainer
{
    private $data;
    private $plugin;

    public function __construct(string $plugin = null)
    {
        $this->plugin = $plugin;
        $this->data = [];
    }

    public function createPlugin(string $name)
    {
        $this->data[$name] = new JinitializeContainer();
        return $this->data;
    }

    public function get(string $key, string $plugin = null)
    {
        if(is_null($plugin)) {
            if(is_null($this->plugin)) throw new \Exception('Something went wrong');
            $plugin = $this->plugin;
        }

        if(! isset($this->data[$plugin])) {
            throw new \Exception("Can't get data from '$plugin' : plugin not found.");
        }

        $pluginContainer = $this->data[$plugin];

        if(! isset($pluginContainer[$key])) {
            throw new \Exception("Key '$key' is not set for plugin '$plugin'");
        }

        return $pluginContainer[$key];
    }
}
