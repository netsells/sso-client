# SSO Client
Client for the Netsells SSO server.

## Installation
Add to composer:
```bash
composer require netsells/sso-client
```

If you are not using Laravel 5.5, you need to add the service provider to your app.php:
```php
Netsells\SSOClient\ServiceProvider::class,
```

Add the following environment variables
```
SSO_URL=https://sso.service.com
SSO_CLIENT_ID=
SSO_CLIENT_SECRET=
```

Add the SSO Auth middleware to any protected routes - these are typically the same routes you protect using the `auth` middleware provided by Laravel.

In your app/Http/Kernel.php add at the bottom of the `$routeMiddleware` array:
```php
'sso' => \Netsells\SSOClient\AuthMiddleware::class,
```

### User Provider Setup

The SSO client has two available modes, it can either use your existing auth provider (such as the laravel eloquent auth) or you can use the sso auth user provider (this is typically used when you do not want to store user info, just secure the site).

#### Existing eloquent setup
The SSO middleware expects your auth config to be correctly configured. Specifically `auth.providers.users.model` as this is the model that is populated and authenticated on your behalf.

Buy default, the user is created if it does not exist based on the email sent by the SSO server. Should you wish to add more information to the User model (or any other model), you should add the following call to your AppServiceProvider. Have a look at the SSOUser DTO to see what information you can get from the SSO server.
```php
SSOClient::setUserCallback(function ($user, \Netsells\SSOClient\User $data) {
    $user->first_name = $data->first_name;

    return $user;
});
```

If you wish to run some code when a user is created or updated via the SSO middleware, you can create the methods `ssoUserWasCreated` and `ssoUserWasUpdated` on the model.

#### No Database setup
All you need to do is set the config, `auth.providers.users.driver` to `sso`. You can now use `Auth::user()` which will return an SSO User instead of a Laravel user.

## Token Provider

When using the SSO as an API token provider, you must either use passport or setup `tymon/jwt-auth ^1.0.0`.

You can handle token requests by adding this route:
```php
Route::get('token', function (Request $request) {
    $auth = app(\Netsells\SSOClient\Authenticator::class);
    return $auth->responseForTokenRequest($request);
})
```