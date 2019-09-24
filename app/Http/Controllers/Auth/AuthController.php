<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\User;
use App\UserInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use AuthenticatesUsers;

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }



    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }
    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }
    protected function forceLogin($id)
    {
        return Auth::loginUsingId($id);
    }

    protected function addRow(Request $request, $user)
    {
        DB::update('update invites set used_at = now(), used_by = "'.$request->email.'" where invite like "'.$request->invite.'"', [1]);
        DB::insert('insert into history (date, action, userid, details) values (now(), "user_registered", '.$user->id.', "'.$request->email.'")', [1]);
        $userType = DB::select('select type from invites where invite like "'.$request->invite.'"', [1]);
        DB::update('update users set role = '.$userType[0]->type.' where email like "'.$request->email.'"', [1]);
        DB::insert('insert into userinfo (created_at, updated_at, name) values (now(), now(), "")', [1]);
        DB::insert('insert into social_ids (created_at, updated_at) values (now(), now())', [1]);
        DB::insert('insert into social_active (updated_at) values (now())', [1]);
        DB::insert('alter table `products_stock` ADD COLUMN `'.$user->id.'` int(9) NULL DEFAULT 0', [1]);
        /*
        DB::table('userinfo')->insert(
         ['name' => '']
         ['name' => '']
         ['name' => '']
        );
        */
    }
    protected function deleteRow($id, $email)
    {
        DB::insert('insert into history (date, userid, action, details) values (now(), "","user_deleted", "Адрес: '.$email.'")', [1]);
        DB::delete('delete from users where id = '.$id, [1]);
        DB::delete('delete from userinfo where id = '.$id, [1]);
        DB::delete('delete from social_ids where id = '.$id, [1]);
        DB::delete('delete from social_active where id = '.$id, [1]);
        DB::delete('alter table products_stock remove column `'.$id.'`', [1]);

    }
    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */


    protected function registered(Request $request, $user)
    {
        $user->generateToken();
        $this->addRow($request, $user);

        return response()->json(['data' => $user->toArray()], 201);
    }


    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'invite' => ['required', 'string',
                Rule::exists('invites')->where(function ($query) {
                    $query->where('used_at', null);
                })],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function login(Request $request)
    {
        if ($request->id != '') {
            if ($this->forceLogin($request->id)) {
                $user = $this->guard()->user();
                $user->generateToken();

                return response()->json([
                    'data' => $user->toArray(),
                ], 200);
            }
        }
        else {
            $this->validateLogin($request);
            if ($this->attemptLogin($request)) {
                $user = $this->guard()->user();
                $user->generateToken();

                return response()->json([
                    'data' => $user->toArray(),
                ], 200);
            }
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
/*
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
*/
    public function getKey($id)
    {
        $result = DB::select('select api_token from users where id = '.$id, [1]);
        return $result;
    }

    public function changePassword(Request $request)
    {
        $newpwd = Hash::make($request->password);
        DB::update('update users set 
        updated_at = now(),        
        password = "'.$newpwd.'"
        where id = '.$request->id, [1]);
        return $newpwd;
    }
}
