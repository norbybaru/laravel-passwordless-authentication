<?php

return [

    'callback_url' => '/callback/login',

    'provider' => 'users',

    'table' => 'passwordless_auth',

    'expire' => 60 * 60,

    'throttle' => 60,

    'magic_link_timeout' => 60 * 60,
];
