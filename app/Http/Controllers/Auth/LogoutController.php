<?php

namespace App\Http\Controllers\Auth;

use App\Models\SsoToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke()
    {
        return view('auth.logout');
    }
}
