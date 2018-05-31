<?php

namespace Nonetallt\Jinitialize\Testing\Constraints;

use Nonetallt\Jinitialize\JinitializeContainer;

class ContainerEquals extends ParentConstraint
{
    public function matches($other)
    {
        $container = JinitializeContainer::getInstance();
        $values = [];

        /* If plugin is not specified, get all containers */
        if(is_null($this->plugin)) {
            foreach($container->getPlugins() as $plugin) {
                $values = array_merge($values, $plugin->getContainer()->getData());
            }
        }
        else {
            $values = $container->getPlugin($this->plugin)->getContainer()->getData();
        }

        return $values === $other;
    }

    public function toString()
    {
        $container = JinitializeContainer::getInstance();

        return "is equal to application container $container";
    }
}
