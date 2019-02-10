<?php

namespace Netsells\SSOClient;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticator
{
    private $urlGenerator;
    /**
     * @var Auth
     */
    private $auth;
    private $redirectUrl;

    public function __construct(UrlGenerator $urlGenerator, Auth $auth)
    {
        $this->urlGenerator = $urlGenerator;
        $this->auth = $auth;
    }

    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function handleUserCreation(User $ssoUser)
    {
        if (config('auth.providers.users.driver', 'eloquent') !== 'sso') {
            $userClass = config('auth.providers.users.model');
            $user = (new $userClass)->firstOrNew(['email' => $ssoUser->email]);

            if (is_callable(SSOClient::getUserCallback())) {
                $closure = SSOClient::getUserCallback();
                $user = $closure($user, $ssoUser);
            }

            $userExisted = $user->exists();

            $user->save();

            $this->triggerUserMethods($user, $userExisted);

            return $user;
        }

        return $ssoUser;
    }

    public function responseForTokenRequest(Request $request) : JsonResponse
    {
        if ($request->has('redirect_url')) {
            $this->setRedirectUrl($request->redirect_url);
        }

        $middleware = app(AuthMiddleware::class);
        $middleware->setAuthenticator($this);

        $middlewareResponse = $middleware->handle($request, function () {});

        if ($middlewareResponse instanceof \Illuminate\Http\RedirectResponse) {
            return response()->json([
                'redirect_url' => $middlewareResponse->getTargetUrl(),
            ]);
        }

        if ($this->auth->check()) {
            $user = $this->auth->user()->id;
            $token = $this->auth->tokenById($user);

            return response()->json(['token' => $token]);
        } else {
            return response()->make(null, 500);
        }
    }

    public function triggerUserMethods($user, $userExisted = true)
    {
        if ($userExisted) {
            if (method_exists($user, 'ssoUserWasUpdated')) {
                $user->ssoUserWasUpdated();
            }
        } else {
            if (method_exists($user, 'ssoUserWasCreated')) {
                $user->ssoUserWasCreated();
            }
        }
    }

    public function generateSSOAuthUrl()
    {
        return env('SSO_URL') . '/auth?client_id=' . env('SSO_CLIENT_ID') . '&url=' . $this->getRedirectUrl();
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        if (!$this->redirectUrl) {
            return $this->urlGenerator->current();
        }

        return $this->redirectUrl;
    }
}