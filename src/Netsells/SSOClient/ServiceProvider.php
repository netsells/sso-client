<?php

namespace Netsells\SSOClient;

use Netsells\SSOClient\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;
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
            $guard = new Guard(Auth::createUserProvider($config['provider']), $this->app['request']);

            if (!$guard->isValid()) {
                // Fallback to eloquent
                $guard = $app['auth']->createSessionDriver($name, $config);
            }

            return $guard;
        });

        $this->app['auth']->provider('sso', function ($app, array $config) {
            return new EloquentUserProvider($app['hash'], $config['model']);
        });
    }
}
