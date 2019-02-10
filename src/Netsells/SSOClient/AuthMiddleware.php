<?php

namespace Netsells\SSOClient;

use Closure;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthMiddleware
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Guard
     */
    private $auth;
    /**
     * @var Redirector
     */
    private $redirector;
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * SSOAuthMiddleware constructor.
     * @param Session $session
     * @param Auth $auth
     * @param Redirector $redirector
     */
    public function __construct(Session $session, Auth $auth, Redirector $redirector)
    {
        $this->session = $session;
        $this->auth = $auth;
        $this->redirector = $redirector;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param Authenticator $authenticator
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If we are not logged in, check for an SSO token
        if (!$this->auth->check()) {
            // If the session already has an SSO token, no poi
            if ($request->has('sso_token') || $this->session->has('sso_token')) {
                if ($request->has('sso_token')) {
                    $this->session->put('sso_token', $request->get('sso_token'));
                }

                // Fetch the user
                $client = app(Client::class);
                $ssoUser = $client->fetchUserByToken($this->session->get('sso_token'));

                $loginUser = $this->authenticator->handleUserCreation($ssoUser);

                $this->auth->login($loginUser);

                return $next($request);
            }

            // Send user to auth
            return $this->redirector->to($this->authenticator->generateSSOAuthUrl());
        }

        return $next($request);
    }

    /**
     * @return Authenticator
     */
    public function getAuthenticator(): Authenticator
    {
        if (!$this->authenticator) {
            $this->authenticator = app(Authenticator::class);
        }

        return $this->authenticator;
    }

    /**
     * @param Authenticator $authenticator
     */
    public function setAuthenticator(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }
}
