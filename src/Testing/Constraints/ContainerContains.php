<?php

namespace Nonetallt\Jinitialize\Testing\Constraints;

use Nonetallt\Jinitialize\JinitializeContainer;

class ContainerContains extends ParentConstraint
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

        foreach($other as $key => $value) {
            if(!isset($values[$key])) return false;
            if($values[$key] !== $value) return false;
        }
        return true;
    }

    public function toString()
    {
        return "is contained in application container";
    }
}
