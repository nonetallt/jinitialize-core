<?php

namespace Nonetallt\Jinitialize\Helpers;

class Strings
{

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
}
