<?php namespace NorbyBaru\Passwordless\Controller;

use App\Http\Controllers\Controller;
use NorbyBaru\Passwordless\Traits\PasswordlessAuth;

/**
 * Class PasswordlessController
 * @package NorbyBaru\Passwordless\Controller
 */
class PasswordlessController extends Controller
{
    use PasswordlessAuth;
}
