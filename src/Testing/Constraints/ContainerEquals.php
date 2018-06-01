<?php

namespace Nonetallt\Jinitialize\Testing\Constraints;

use Nonetallt\Jinitialize\JinitializeContainer;

class ContainerEquals extends ParentConstraint
{
    public function matches($other)
    {
        $container = JinitializeContainer::getInstance();
        $values = [];

        if(! is_null($this->plugin)) {
            return $other === $container->getData($this->plugin);
        }

        return $other === $container->getData();
    }

    public function toString()
    {
        $container = JinitializeContainer::getInstance();

        return "is equal to application container $container";
    }
}
