<?php

namespace Netsells\SSOClient;

use Closure;

class SSOClient
{
    /**
     * @var Closure
     */
    protected static $userCallback = null;

    /**
     * @param Closure $closure
     * @return void
     */
    public static function setUserCallback(Closure $closure)
    {
        static::$userCallback = $closure;
    }

    /**
     * @return Closure
     */
    public static function getUserCallback()
    {
        return static::$userCallback;
    }
}
