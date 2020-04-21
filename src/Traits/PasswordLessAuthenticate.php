<?php namespace NorbyBaru\Passwordless\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NorbyBaru\Passwordless\Facades\Passwordless;

/**
 *
 * Trait PasswordLessAuthenticate
 * @package NorbyBaru\Passwordless\Traits
 */
trait PasswordLessAuthenticate
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loginByEmail(Request $request)
    {
        $this->verifyMagicLink($request);

        if ($response = $this->authenticatedResponse($request, auth()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect()->intended($this->redirectRoute($request));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
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

        return route('home');
    }

    /**
     * @param Request $request
     *
     * @throws AuthorizationException
     */
    protected function verifyMagicLink(Request $request)
    {
        $request->validate($this->requestRules());

        $user = $this->magicLink()->validateMagicLink($this->requestCredentials($request));

        if (!$user) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $this->requestCredentials($request)['hash'], sha1($user->getEmailForMagicLink()))) {
            throw new AuthorizationException;
        }

        auth()->login($user);
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param mixed   $user
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
            'hash' => 'required'
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
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
