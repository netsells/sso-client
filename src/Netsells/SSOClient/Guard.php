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

    protected $pomerium;
    protected $userProvider;

    /** @var UserService $userService */
    protected $userService;

    public function __construct(UserProvider $provider, Request $request = null)
    {
        $this->userProvider = $provider;
        $this->pomerium = Pomerium::fromRequest($request);
        $this->userService = app(UserService::class);
    }

    /**
     * Determines if we can correctly use this guard.
     * 
     * @return bool 
     */
    public function isValid(): bool
    {
        return $this->pomerium->isValid();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!$this->pomerium->isValid()) {
            return;
        }

        $ssoUser = $this->userService->createSsoUser($this->pomerium->getUserAttributes());

        return $this->userService->handleUserCreation($ssoUser);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return true;
    }

    public function logout()
    {
        return;
    }
}
