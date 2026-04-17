<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user): void
    {
        ActivityLog::log('login', 'auth', $user->id, $user->name);
    }

    protected function loggedOut(Request $request): void
    {
        // user already logged out, log before session cleared via logout()
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            ActivityLog::log('logout', 'auth', $user->id, $user->name);
        }
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect('/');
    }
}
