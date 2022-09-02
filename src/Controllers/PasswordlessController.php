<?php 

namespace NorbyBaru\Passwordless\Controllers;

use Illuminate\Routing\Controller;
use NorbyBaru\Passwordless\Traits\PasswordlessAuth;

/**
 * Class PasswordlessController
 * @package NorbyBaru\Passwordless\Controller
 */
class PasswordlessController extends Controller
{
    use PasswordlessAuth;
}
