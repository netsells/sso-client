<?php

namespace Netsells\SSOClient;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as GuardInterface;

class Guard implements GuardInterface
{
    use GuardHelpers;

    protected Pomerium $pomerium;
    protected UserProvider $userProvider;
    protected UserService $userService;

    public function __construct(UserProvider $provider, Request $request = null)
    {
        $this->userProvider = $provider;
        $this->pomerium = Pomerium::fromRequest($request);
        $this->userService = app(UserService::class);
    }

    public function isValid(): bool
    {
        return $this->pomerium->isValid();
    }

    public function user(): ?Authenticatable
    {
        if (!$this->pomerium->isValid()) {
            return null;
        }

        $ssoUser = $this->userService->createSsoUser($this->pomerium->getUserAttributes());

        return $this->userService->handleUserCreation($ssoUser);
    }

    public function validate(array $credentials = [])
    {
        return true;
    }

    public function logout()
    {
    }
}
