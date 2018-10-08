<?php

namespace smartQQ\Console;

class Console
{
    public function __construct()
    {
    }

    /**
     * determine the console is windows or linux.
     *
     * @return bool
     */
    public static function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
