<?php

namespace Netsells\SSOClient;

use Netsells\SSOClient\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        Pomerium::setPublicKey(env('POMERIUM_PUBLIC_KEY'));

        $this->app['auth']->extend('sso', function ($app, $name, array $config) {
            return new Guard(Auth::createUserProvider($config['provider']), $this->app['request']);
        });

        $this->app['auth']->provider('sso', function ($app, array $config) {
            return new UserProvider($config);
        });
    }
}