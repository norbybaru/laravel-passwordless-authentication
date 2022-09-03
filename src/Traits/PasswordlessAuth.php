<?php

namespace NorbyBaru\Passwordless\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable;
use NorbyBaru\Passwordless\Facades\Passwordless;

/**
 * Trait PasswordLessAuthenticate
 */
trait PasswordlessAuth
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function loginByEmail(Request $request)
    {
        $response = $this->verifyMagicLink($request);

        if (! $response instanceof CanUsePasswordlessAuthenticatable) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [trans($response)],
                ]);
            }

            return redirect()->to($this->redirectRoute($request))
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
        }

        $this->authenticateUser($response);

        if ($response = $this->authenticatedResponse($request, auth()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectRoute($request));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectRoute(Request $request)
    {
        if ($request->get('redirect_to')) {
            return $request->get('redirect_to');
        }

        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return route(config('passwordless.default_redirect_route'));
    }

    /**
     * @param  Request  $request
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|\NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable|null
     *
     * @throws AuthorizationException
     */
    protected function verifyMagicLink(Request $request)
    {
        $request->validate($this->requestRules());

        $user = $this->magicLink()->validateMagicLink($this->requestCredentials($request));

        if (! $user instanceof CanUsePasswordlessAuthenticatable) {
            return $user;
        }

        if (! hash_equals((string) $this->requestCredentials($request)['hash'], sha1($user->getEmailForMagicLink()))) {
            throw new AuthorizationException;
        }

        return $user;
    }

    /**
     * @param $user
     */
    public function authenticateUser($user)
    {
        auth()->login($user);
    }

    /**
     * The user has been authenticated.
     *
     * @param  Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    public function authenticatedResponse(Request $request, $user)
    {
    }

    /**
     * @return array
     */
    protected function requestRules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'hash' => 'required',
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function requestCredentials(Request $request): array
    {
        return $request->only(['email', 'token', 'hash']);
    }

    /**
     * @return \NorbyBaru\Passwordless\MagicLink
     */
    public function magicLink()
    {
        return Passwordless::magicLink();
    }
}
