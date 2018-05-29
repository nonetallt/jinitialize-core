<?php

namespace Nonetallt\Jinitialize;

class Plugin
{
    private $name;
    private $container;
    private $settings;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->container = new PluginContainer($name);
        $this->settings = [];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setSettings(array $settings) 
    {
        $this->settings = $settings;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
