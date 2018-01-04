<?php

namespace Netsells\SSOClient;

use GuzzleHttp\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

class UserProvider implements UserProviderContract
{

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->getSSOUser(['id' => $identifier]);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->getSSOUser();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        return;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->getSSOUser();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return true;
    }

    /**
     * @return User
     */
    protected function getSSOUser()
    {
        $client = new Client(['base_uri' => env('SSO_URL')]);
        $userInfo = $client->get('user', [
            'query' => [
                'client_id' => env('SSO_CLIENT_ID'),
                'client_secret' => env('SSO_CLIENT_SECRET'),
                'token' => \Session::get('sso_token'),
            ],
        ]);

        $userData = json_decode($userInfo->getBody()->getContents());

        return new User((array) $userData);
    }
}