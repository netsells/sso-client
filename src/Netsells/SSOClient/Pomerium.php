<?php

namespace Netsells\SSOClient;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class Pomerium
{
    protected static $publicKey;

    protected const HEADER_NAME = "X-Pomerium-Jwt-Assertion";

    protected $userId;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $photoUrl;
    protected $groups = [];

    public static function setPublicKey($key): void
    {
        static::$publicKey = base64_decode($key);
    }

    public static function fromRequest(Request $request): Pomerium
    {
        $pomerium = new static();

        if ($headerValue = $request->header(Pomerium::HEADER_NAME)) {
            $pomerium->decodeHeader($headerValue);
        }

        return $pomerium;
    }

    private function __construct()
    {
    }

    public function isValid()
    {
        return !is_null($this->userId);
    }

    protected function decodeHeader($headerValue): void
    {
        $jwtContents = JWT::decode($headerValue, static::$publicKey, ['ES256']);

        $this->userId = $jwtContents->sub;
        $this->firstName = $jwtContents->given_name;
        $this->lastName = $jwtContents->family_name;
        $this->fullName = $jwtContents->name;
        $this->email = $jwtContents->email;
        $this->photoUrl = $jwtContents->picture;
        $this->groups = $jwtContents->groups;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    public function getUserAttributes(): array
    {
        return [
            'id' => $this->getUserId(),
            'email' => $this->getEmail(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'name' => $this->getFullName(),
            'photo_url' => $this->getPhotoUrl(),
            'groups' => $this->getGroups(),
        ];
    }
}
