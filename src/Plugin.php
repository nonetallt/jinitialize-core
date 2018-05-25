<?php

namespace Nonetallt\Jinitialize;

class Plugin
{
    private $name;
    private $container;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->container = new PluginContainer($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
