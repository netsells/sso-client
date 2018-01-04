<?php

namespace Netsells\SSOClient;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @property string id
 * @property string token
 * @property string email
 * @property string name
 * @property string oauth_token
 * @property string oauth_refresh_token
 * @property string allowed_access
 * @property string created_at
 * @property string updated_at
 * @property string first_name
 * @property string last_name
 **/
class User implements Authenticatable
{
    protected $attributes = [];

    /**
     * SSOUser constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        return;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return;
    }
}