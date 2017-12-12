<?php

namespace Netsells\SSOClient;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Routing\UrlGenerator;

class SSOAuthMiddleware
{
    /**
     * @var Closure
     */
    protected static $userCallback;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Guard
     */
    private $auth;
    /**
     * @var Redirector
     */
    private $redirector;

    /**
     * SSOAuthMiddleware constructor.
     * @param Session $session
     * @param UrlGenerator $urlGenerator
     * @param Auth $auth
     * @param Redirector $redirector
     */
    public function __construct(Session $session, UrlGenerator $urlGenerator, Auth $auth, Redirector $redirector)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->auth = $auth;
        $this->redirector = $redirector;
    }

    /**
     * @param Closure $closure
     * @return void
     */
    public static function setUserCallback(Closure $closure): void
    {
        static::$userCallback = $closure;
    }

    /**
     * @return Closure
     */
    public static function getUserCallback(): ?Closure
    {
        return static::$userCallback;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
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

                // Try and fetch the user
                $client = new Client(['base_uri' => env('SSO_URL')]);
                $userInfo = $client->get('user', [
                    'query' => [
                        'client_id' => env('SSO_CLIENT_ID'),
                        'client_secret' => env('SSO_CLIENT_SECRET'),
                        'token' => $this->session->get('sso_token'),
                    ],
                ]);

                $userData = json_decode($userInfo->getBody()->getContents());
                $ssoUser = new SSOUser($userData);

                $userClass = config('auth.providers.users.model');
                $user = (new $userClass)->firstOrNew(['email' => $ssoUser->email]);

                if (is_callable(static::$userCallback)) {
                    $user = static::getUserCallback()($user, $ssoUser);
                }

                $user->save();
                $this->auth->login($user);

                return $next($request);
            }

            // Send user to auth
            return $this->redirector->to($this->ssoAuthUrl());
        }

        return $next($request);
    }

    /**
     * Builds the Auth URL
     * @return string
     */
    public function ssoAuthUrl()
    {
        return env('SSO_URL') . '/auth?client_id=' . env('SSO_CLIENT_ID') . '&url=' . $this->urlGenerator->current();
    }
}
