<?php

namespace Netsells\SSOClient;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Session\Session;

class Client
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return GuzzleClient
     */
    protected function getClient()
    {
        return new GuzzleClient(['base_uri' => env('SSO_URL')]);
    }

    /**
     * @param $token
     * @return User
     */
    public function fetchUserByToken($token)
    {
        $client = $this->getClient();
        $userInfo = $client->get('user', [
            'query' => [
                'client_id' => env('SSO_CLIENT_ID'),
                'client_secret' => env('SSO_CLIENT_SECRET'),
                'token' => $token,
            ],
        ]);

        $userData = json_decode($userInfo->getBody()->getContents());
        return new User((array) $userData);
    }

    /**
     * @return User
     */
    public function fetchUser()
    {
        $token = $this->getToken();

        return $this->fetchUserByToken($token);
    }

    /**
     * @return mixed
     */
    private function getToken()
    {
        return $this->session->get('sso_token');
    }
}