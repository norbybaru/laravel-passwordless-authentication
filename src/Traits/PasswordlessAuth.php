<?php

namespace NorbyBaru\Passwordless\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable;
use NorbyBaru\Passwordless\Facades\Passwordless;
use NorbyBaru\Passwordless\MagicLink;

trait PasswordlessAuth
{
    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function loginByEmail(Request $request): RedirectResponse|Response
    {
        $response = $this->verifyMagicLink($request);

        if (! $response instanceof CanUsePasswordlessAuthenticatable) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [trans($response)],
                ]);
            }

            return redirect()->to($this->redirectRoute($request, false))
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

    protected function redirectRoute(Request $request, bool $success = true): string
    {
        if ($request->get('redirect_to')) {
            return $request->get('redirect_to');
        }

        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        $routeName = config('passwordless.default_redirect_route');

        if (! $success) {
            $routeName = config('passwordless.login_route');
        }

        return route($routeName);
    }

    protected function verifyMagicLink(Request $request): string|Authenticatable|CanUsePasswordlessAuthenticatable
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
     * @return RedirectResponse|Response|null
     */
    public function authenticatedResponse(Request $request, $user)
    {
        return null;
    }

    protected function requestRules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'hash' => 'required',
        ];
    }

    protected function requestCredentials(Request $request): array
    {
        return $request->only(['email', 'token', 'hash']);
    }

    public function magicLink(): MagicLink
    {
        return Passwordless::magicLink();
    }
}
