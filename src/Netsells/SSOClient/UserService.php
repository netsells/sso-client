<?php

namespace Netsells\SSOClient;

use Illuminate\Contracts\Auth\Authenticatable;

class UserService
{
    public function handleUserCreation(User $ssoUser): Authenticatable
    {
        /**
         * We're not actually handling user providers correctly, however this is
         * internal so it doesn't matter.
         */
        if (config('auth.providers.users.driver', 'eloquent') !== 'sso') {
            $userClass = config('auth.providers.users.model');
            $user = (new $userClass())->firstOrNew(['email' => $ssoUser->email]);

            if (is_callable(SSOClient::getUserCallback())) {
                $closure = SSOClient::getUserCallback();
                $user = $closure($user, $ssoUser);
            }

            $userExisted = $user->exists();

            $user->save();

            $this->triggerUserMethods($user, $userExisted);

            return $user;
        }

        return $ssoUser;
    }

    public function triggerUserMethods($user, $userExisted = true): void
    {
        if ($userExisted) {
            if (method_exists($user, 'ssoUserWasUpdated')) {
                $user->ssoUserWasUpdated();
            }
        } else {
            if (method_exists($user, 'ssoUserWasCreated')) {
                $user->ssoUserWasCreated();
            }
        }
    }

    public function createSsoUser($attributes): User
    {
        return new User($attributes);
    }
}
