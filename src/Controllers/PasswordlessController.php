<?php

namespace NorbyBaru\Passwordless\Controllers;

use Illuminate\Routing\Controller;
use NorbyBaru\Passwordless\Traits\PasswordlessAuth;

class PasswordlessController extends Controller
{
    use PasswordlessAuth;
}
