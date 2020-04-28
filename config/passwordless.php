<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Callback URL to login
    |--------------------------------------------------------------------------
    |
    | This URL path can be customized to suit user url structure or preference.
    |
    */

    'callback_url' => '/callback/login',

    /*
    |--------------------------------------------------------------------------
    | Auth Provider
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'provider' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    |
    */

    'table' => 'passwordless_auth',

    /*
    |--------------------------------------------------------------------------
    | Token Expiry time
    |--------------------------------------------------------------------------
    |
    | The expire time is the number of seconds that token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'expire' => 60 * 60,

    /*
    |--------------------------------------------------------------------------
    | Throttle
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'throttle' => 60,

    /*
    |--------------------------------------------------------------------------
    | Magic Link Time out
    |--------------------------------------------------------------------------
    |
    | The expire time is the number of minutes that the magic link signature should be
    | considered valid. This security feature keeps signature short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'magic_link_timeout' => 60,
];
