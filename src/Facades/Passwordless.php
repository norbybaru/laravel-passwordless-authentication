<?php namespace NorbyBaru\Passwordless\Facades;


use Illuminate\Support\Facades\Facade;

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
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.passwordless';
    }
}
