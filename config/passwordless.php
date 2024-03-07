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
    | Default route name for redirect
    |--------------------------------------------------------------------------
    |
    | Set default public route name to redirect authenticated user when successfully authenticated or failure to authenticate.
    | This route name will only be apply when no intended url has been stored in the session to redirect user
    | when trying to access auth page and no 'redirect_to' query params is found on the url
    |
    */
    'default_redirect_route' => 'home',

    /*
    |--------------------------------------------------------------------------
    | Login route name
    |--------------------------------------------------------------------------
    |
    | Set current login route name of your application to give the package ability to redirect to the page
    | whenever an issue occurred with magic link validation
    |
    */
    'login_route' => 'login',

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
    | Amount of seconds to wait before generating and sending a new magic link to User.
    | Throttling is mechanism to prevent spamming user and exhausting system resource
    |
    */
    'throttle' => 60,

    /*
    |--------------------------------------------------------------------------
    | Magic Link Time out
    |--------------------------------------------------------------------------
    |
    | The expire time is the number of seconds that the magic link signature should be
    | considered valid. This security feature keeps signature short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */
    'magic_link_timeout' => 60 * 60,
];
