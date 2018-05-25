<?php

namespace Nonetallt\Jinitialize\Testing\Constraints;

abstract class ParentConstraint extends \PHPUnit_Framework_Constraint
{
    private $attributes = [];

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __get($key)
    {
        if(!isset($this->attributes[$key])) return null;
        return $this->attributes[$key];
    }
}
