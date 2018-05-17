<?php

namespace Nonetallt\Validation;

class Jvalidator
{
    public static function mySqlDatabaseName(string $name)
    {
        return preg_match('|^[0-9a-zA-Z$_]+$|', $name) === 1;
    }

    public static function filename(string $filename)
    {
        $invalid = [
            '<',
            ':',
            '"',
            '/',
            '\\',
            '|',
            '?',
            '*',
            '<',
        ];
    }
}
