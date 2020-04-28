<?php namespace NorbyBaru\Passwordless\Facades;


use Illuminate\Support\Facades\Facade;
use NorbyBaru\Passwordless\MagicLink;

/**
 * Class Passwordless
 *
 * @method static \NorbyBaru\Passwordless\MagicLink magicLink()
 *
 * @see \NorbyBaru\Passwordless\MagicLink
 * @package NorbyBaru\Passwordless\Facades
 */
class Passwordless extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const MAGIC_LINK_SENT = MagicLink::MAGIC_LINK_SENT;

    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const MAGIC_LINK_VERIFIED = MagicLink::MAGIC_LINK_VERIFIED;

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = MagicLink::INVALID_USER;

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = MagicLink::INVALID_TOKEN;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.passwordless';
    }
}
