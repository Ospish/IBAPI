<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    }
    protected function guard()
    {
        return Auth::guard();
    }
    
    public function login(Request $request)
    {
        $this->validateLogin($request);
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();

            return response()->json([
                'data' => $user->toArray(),
            ], 200);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        $user = $this->guard()->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
            $this->guard()->logout();
        }

        return response()->json(['data' => 'User logged out.'], 200);
    }

    public function isLoggedIn($id)
    {
        if ($this->guard()->user()) {
            $result = DB::select('select api_token from users where id = '.$id, [1]);
            return $result;
        }
        else {
            response()->json(['data' => 'Not Logged In'], 200);
        }
    }


    public function getKey($id)
    {
        $result = DB::select('select api_token from users where id = '.$id, [1]);
        return $result;
    }

    public function changePassword(Request $request)
    {
        Log::debug($this->validateLogin($request));
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();

            return response()->json([
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }
}
