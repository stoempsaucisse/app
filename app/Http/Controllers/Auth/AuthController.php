<?php

namespace Microffice\Http\Controllers\Auth;

use Microffice\User;
use DB;
use Validator;
use Microffice\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
// use Microffice\Http\Controllers\Auth\AuthenticatesUsers;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the authentication of existing users.
    | By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;
    // use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * The attribute to use for authentication 
     * [email | name | login] (default is email)
     * login means that both are allowed).
     *
     * @var string
     */
    protected $username = 'login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get the needed authorization credentials from the request.
     * This has been modified to allow both login types aka. 'name' and 'email'
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        if( $this->loginUsername() == 'login' ) {
            $credentials = $request->only('password');
            $validator = $this->getValidationFactory()->make(['login' => $request->input('login')], ['login' => 'email'], [], []);
            $login = $validator->fails() ? 'name' : 'email';
            $credentials[$login] = $request->input('login');
            return $credentials;
        } else {
            return $request->only($this->loginUsername(), 'password');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, User::$rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
