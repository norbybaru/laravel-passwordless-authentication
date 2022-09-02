<?php
namespace NorbyBaru\Passwordless\Tests\Fixtures;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index()
    {
        return response(Auth::user()->name, 200);
    }

    public function redirect()
    {
        return response('Redirected ' . Auth::user()->name, 200);
    }
}