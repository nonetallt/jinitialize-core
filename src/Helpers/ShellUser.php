<?php

namespace Nonetallt\Jinitialize\Helpers;

class ShellUser
{
    private static $user = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if(is_null(self::$user)) {
            self::$user = new self();
        }

        return self::$user;
    }

    public function isRoot()
    {
        return $this->getName() === 'root';
    }

    public function getName()
    {
        return $this->getInfo()['name'];
    }

    public function getInfo()
    {   
        return posix_getpwuid($this->getId());
    }

    public function getId()
    {
        return posix_geteuid();
    }
}
