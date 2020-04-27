# SSO Client
Client for the Netsells SSO. This consumes the headers from [Pomerium](https://www.pomerium.io/) and integrates them with Laravel's auth system.

## Installation
Add to composer:
```bash
composer require netsells/sso-client
```

Add the following environment variable
```
POMERIUM_PUBLIC_KEY=
```

This is the base64 encoded public key. This is used to detemine that the JWT data came from Pomerium and not an attacker.

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