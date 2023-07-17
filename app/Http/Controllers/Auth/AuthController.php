<?php

namespace App\Http\Controllers\Auth;

// use App\Helpers\Helper;
use App\Http\Controllers\Controller;
// use App\Models\Content;
// use App\Models\RoleUser;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use Crypt;
use Hash;
use Validator;
use App\Models\IPAM;

use Illuminate\Support\Facades\Input;

class AuthController extends Controller
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

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/welcome';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
   
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ]);
    }

    public function login(Request $request)
    {
        if(isset($request['_token']) && $request['_token']!='')
        {

            $email = $request['email'];
            $password = $request['password'];

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',

            ]);

            if ($validator->fails()) {
                Session::flash('message_danger', 'These credentials do not match our records 001.');
                return back()->withInput();
            }

            if (Auth::attempt(['email' => $email, 'password' => $password])) 
            {
                //log ip address
                IPAM::log_ip();

                Session::put('email', $email);

                return redirect()->intended('/ipam');

            } else {

                Session::flash('message_danger', 'Invalid Login.');
                return back()->withInput();

            }

        }else{

            return redirect()->away(env('SURL').'/signup');

        }
    }

    public function logout(Request $request)
    {        
        //set user to offline
        if(isset(Auth::user()->id))
        {
            //logout user
            Auth::guard()->logout();
        }

        return redirect('/login');
    }

}
