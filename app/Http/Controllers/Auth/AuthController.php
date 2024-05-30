<?php

namespace App\Http\Controllers\Auth;

use App\User;
use DB;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;
	
	private $maxLoginAttempts = 10;
	
	protected $username = 'username';
	//protected $redirectPath = '/dashboard';	
	

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
		$this->auth = $auth;
        $this->middleware('guest', ['except' => 'getLogout']);
		
		
    }
	
	
	

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' 		=> 'required|max:255',
            'email' 	=> 'required|email|max:255',
			'emp_id' 	=> 'require|integer',
			'username'	=> 'required|max:255|unique:users',
            'password' 	=> 'required|confirmed|min:6',
        ]);
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
			'emp_id' => $data['emp_id'],
            'username' => $data['username'],
        ]);
    }
}
