<?php

namespace Netsells\SSOClient;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @property string id
 * @property string email
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string photo_url
 * @property array groups
 **/
class User implements Authenticatable
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __isset($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    public function __set($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function setAttributes($attributes = []): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    public function getAuthPassword()
    {
    }

    public function getRememberToken()
    {
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
    }
}
