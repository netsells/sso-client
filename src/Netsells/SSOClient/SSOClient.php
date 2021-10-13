<?php

namespace Netsells\SSOClient;

use Closure;

class SSOClient
{
    protected static ?Closure $userCallback = null;

    public static function setUserCallback(Closure $closure)
    {
        static::$userCallback = $closure;
    }

    public static function getUserCallback()
    {
        return static::$userCallback;
    }
}
