<?php

namespace Netsells\SSOClient;

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
        $this->app['auth']->provider('sso', function ($app, array $config) {
            return new UserProvider($config);
        });
    }
}