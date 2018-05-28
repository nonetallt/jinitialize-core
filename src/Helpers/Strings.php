<?php

namespace Nonetallt\Jinitialize\Helpers;

class Strings
{

    /* str_after */
    public static function cutAfter(string $subject, string $until)
    {
        $stopAt = strpos($subject, $until);
        $cut = null;

        if($stopAt === false) {
            $cut = substr($subject, 0);
        }
        else {
            $cut = substr($subject, 0, $stopAt);
        }
        return $cut;
    }

    /* str_until */
    public static function cutUntil(string $subject, string $until, bool $includeStop = true)
    {
        $startAt = strpos($subject, $until);
        $cut = null;

        if($startAt === false) {
            $cut = substr($subject, 0);
        }
        else {
            if(! $includeStop) $startAt++;
            $cut = substr($subject, $startAt);
        }
        return $cut;
    }

    public static function packageNamespace(string $author, $pluginName)
    {
        $pluginParts = explode('-', $pluginName);

        array_walk($pluginParts, function(&$part) {
            $part = ucfirst($part) . '\\\\';
        });

        $namespace = ucfirst($author) . '\\\\' . implode('', $pluginParts);
        return $namespace;
    }

    public static function afterLast(string $subject, string $last)
    {
        $parts = explode($last, $subject);
        return $parts[count($parts) -1];
    }

    public static function untilLast(string $subject, string $last)
    {
        $parts = explode($last, $subject);

        /* Remove last part from the array */
        array_pop($parts);

        return implode($last, $parts);
    }

    /* str_replace_first */
    public static function replaceFirst(string $target, string $replacement, string $subject)
    {
        $pos = strpos($subject, $target);

        /* No operations needed if target does not exist */
        if($pos === false) return $subject;

        $str = '';

        for($n = 0; $n < strlen($subject); $n++) {
            if($n < $pos || $n > $pos + strlen($target) -1) {
                $str .= substr($subject, $n, 1);
            }
            else {
                $str .= substr($replacement, $n - $pos, 1);
            }
        }

        return $str;
    }

    public static function toSnakeCase(string $string)
    {
        /* lowercase first */
        $string = lcfirst($string);

        $output = '';

        for($n = 0; $n < strlen($string); $n++) {
            $char = substr($string, $n, 1);
            if(ctype_upper($char)) {
                $output .= '_';
            }
            $output .= $char;
        }
        return strtolower($output);
    }
}
