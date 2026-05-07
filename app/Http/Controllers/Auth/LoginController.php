<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Throwable;

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

    /**
     * Handle JWT token-based login via GET /token-login?token=<jwt>
     * This route is completely separate from the normal login flow.
     */
    public function handleTokenLogin(Request $request)
    {
        $token = $request->query('token');
		
        if (empty($token)) {
            return redirect()->route('login')->with('error', 'No token provided.');
        }

        $secretKey = config('app.jwt_secret', env('JWT_SECRET', ''));

        if (empty($secretKey)) {
            return redirect()->route('login')->with('error', 'Token login is not configured.');
        }

        // ── Decode & verify the JWT ──────────────────────────────────────────
        
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
		
      
		
        // ── Validate the `sub` claim (employee_id) ───────────────────────────
        if (empty($decoded->sub)) {
            return redirect()->route('login')->with('error', 'Token is missing the required subject (sub) claim.');
        }

        $employeeId = (string) $decoded->sub;
        // Optional: if employeeid is also passed, it must match sub
        $requestedEmployeeId = $request->query('employeeid');
        if ($requestedEmployeeId !== null && (string) $requestedEmployeeId !== $employeeId) {
            return redirect()->route('login')->with('error', 'Token subject does not match the provided employee ID.');
        }

        // ── Look up the active user ──────────────────────────────────────────
        $user = User::where('employee_id', $employeeId)
            ->where('status', 1)
            ->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'No active account found for this token.');
        }

        // ── Session conflict guard ───────────────────────────────────────────
        if (Session::has('authenticated') && Session::get('employee_id') != $user->employee_id) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Previous user session terminated. Please try again.');
        }

        // ── Already logged in as the same user — just redirect ───────────────
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->intended($this->redirectTo);
        }

        // ── Log in the user (no password check) ─────────────────────────────
        Auth::login($user, false);

        $request->session()->regenerate();

        ActivityLog::log('login', 'auth', $user->id, $user->name);

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Normal credential-based login (unchanged).
     */
    public function login(Request $request)
    {
        return $this->normalLogin($request);
    }

    protected function normalLogin(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
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
