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

        $pomerium->decodeHeader('eyJhbGciOiAiRVMyNTYifQ.eyJwaWN0dXJlIjogImh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hLS9BT2gxNEdoX3hTeklacGtjd1NtaWRtb2ZraGRfSkthU0xkYTFJUWhCWTlmX25BPXM5Ni1jIiwgInByb2dyYW1hdGljIjogZmFsc2UsICJlbWFpbF92ZXJpZmllZCI6IHRydWUsICJmYW1pbHlfbmFtZSI6ICJKb3JkYW4iLCAiZ3JvdXBzIjogWyJhZG1pbkBuZXRzZWxscy5jby51ayIsICJhbGxAbmV0c2VsbHMuY28udWsiLCAiYXBwbGUtc3RvcmVAbmV0c2VsbHMuY28udWsiLCAiYXBwc3RvcmVAbmV0c2VsbHMuY28udWsiLCAiYmFja2VuZEBuZXRzZWxscy5jby51ayIsICJiZXR0eUBuZXRzZWxscy5jby51ayIsICJib3RAbmV0c2VsbHMuY28udWsiLCAiYndwLWNpdHlnYXRlQG5ldHNlbGxzLmNvLnVrIiwgImNhcHN1bGVAbmV0c2VsbHMuY28udWsiLCAiY29ybmVyLXBpbkBuZXRzZWxscy5jby51ayIsICJjcm9uLWpvYnNAbmV0c2VsbHMuY28udWsiLCAiZHJAbmV0c2VsbHMuY28udWsiLCAiZnJlZWxhbmNlcnNAbmV0c2VsbHMuY28udWsiLCAiZnJvbnRlbmRAbmV0c2VsbHMuY28udWsiLCAiaGVsbG9AbmV0c2VsbHMuY28udWsiLCAiaW5mb0BpcGhvbmUtY29uZmlnLmNvbSIsICJpbnRAbmV0c2VsbHMuY28udWsiLCAiaW52b2ljZXNAbmV0c2VsbHMuY28udWsiLCAiaW9zQG5ldHNlbGxzLmNvLnVrIiwgImpvYi1iYWNrZW5kLWRldmVsb3BlckBuZXRzZWxscy5jby51ayIsICJqb2JzQG5ldHNlbGxzLmNvLnVrIiwgImxlYWRzQG5ldHNlbGxzLmNvLnVrIiwgImxlYXJuQG5ldHNlbGxzLmNvLnVrIiwgImxlZ2FsQG5ldHNlbGxzLmNvLnVrIiwgIm1vYmlsZS10ZWFtQG5ldHNlbGxzLmNvLnVrIiwgIm9mZmljZUBuZXRzZWxscy5jby51ayIsICJwYXlwYWxAbmV0c2VsbHMuY28udWsiLCAicHJvamVjdC1iYWdib2FyZEBuZXRzZWxscy5jby51ayIsICJwcm9qZWN0LWZlcmEtZm9vZC1pbnRlZ3JpdHlAbmV0c2VsbHMuY28udWsiLCAicHJvamVjdC1pd2MtcmVjb21tZW5kYXRpb25zLWRhdGFiYXNlQG5ldHNlbGxzLmNvLnVrIiwgInJlYmVjY2Euam9yZGFuQG5ldHNlbGxzLmNvLnVrIiwgInJlY3J1aXRlcnNAbmV0c2VsbHMuY28udWsiLCAic2Ftc0BuZXRzZWxscy5jby51ayIsICJzdGFydGVyc0BuZXRzZWxscy5jby51ayIsICJzdXBwb3J0LWFsZXJ0c0BuZXRzZWxscy5jby51ayIsICJzeXNhZG1pbnNAbmV0c2VsbHMuY28udWsiLCAidGVhbS1wYXBhQG5ldHNlbGxzLmNvLnVrIiwgInRvLXBheUBuZXRzZWxscy5jby51ayJdLCAibmJmIjogMTU4Nzk3Nzk4NCwgImF0aSI6ICI0Y2Y3OWU4MTQ3MDBiMGQ2IiwgImV4cCI6IDE1ODc5ODE1ODIsICJnaXZlbl9uYW1lIjogIlNhbSIsICJhdWQiOiBbImF1dGgubmV0c2VsbHMudG9vbHMiLCAicGVvcGxlLm5ldHNlbGxzLnRvb2xzIl0sICJpYXQiOiAxNTg3OTc3OTg0LCAibmFtZSI6ICJTYW0gSm9yZGFuIiwgImVtYWlsIjogInNhbUBuZXRzZWxscy5jby51ayIsICJpc3MiOiAiYXV0aC5uZXRzZWxscy50b29scyIsICJzdWIiOiAiMTEwNDU1Njk5MjMzNTA1NDU2ODA5In0.vzs5R4nj8_m3UwOGI1mRCYMuquqL8d803t6ppJ3UBg86jSftoOjYqi7uxx9l0PXgLRaUB8Euxbwn7WzkFfm2kg');

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