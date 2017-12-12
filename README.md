# SSO Client
Client for the Netsells SSO server.

## Installation
Add to composer:
```bash
composer require netsells/sso-client
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
'sso' => Netsells\SSOClient\SSOAuthMiddleware::class,
```

The SSO middleware expects your auth config to be correctly configured. Specifically `auth.providers.users.model` as this is the model that is populated and authenticated on your behalf.

Buy default, the user is created if it does not exist based on the email sent by the SSO server. Should you wish to add more information to the User model (or any other model), you should add the following call to your AppServiceProvider. Have a look at the SSOUser DTO to see what information you can get from the SSO server.
```php
SSOAuthMiddleware::setUserCallback(function ($user, \Netsells\SSOClient\SSOUser $data) {
    $user->name = $data->name;

    return $user;
});
```