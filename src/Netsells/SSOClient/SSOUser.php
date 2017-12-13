<?php

namespace Netsells\SSOClient;

class SSOUser
{
    public $id;
    public $name;
    public $first_name;
    public $last_name;
    public $email;
    public $accessToken;
    public $refreshToken;

    public function __construct($data)
    {
        $this->id = $data->id;
        $this->email = $data->email;

        $this->name = $data->name;
        $this->first_name = $data->first_name;
        $this->last_name = $data->last_name;

        $this->accessToken = $data->oauth_token;
        $this->refreshToken = $data->oauth_refresh_token;
    }
}