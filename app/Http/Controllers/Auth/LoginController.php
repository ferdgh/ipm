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

use Illuminate\Support\Facades\Input;

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

            if (Auth::attempt(['email' => $email, 'password' => $password, 'activated' => 1])) 
            {
                // $info = DB::table('users')
                //         ->select(
                //             'id',
                //             'created_at',
                //             'name',
                //             'oto6',
                //             'oto10',
                //             'vendorID',
                //             'obd_seen',
                //             'is_trial',
                //             'agreeToJohnCNotes',
                //             'first_name',
                //             'last_name',
                //             'phone',
                //             'country_code',
                //             'verified',
                //             'hide_message',
                //             'expiry',
                //             'cookie_policy',
                //             'gdrp',
                //             'hide_tos',
                //             'hide_welcome',
                //             'hide_slingly_offers',
                //             'unlock_all_courses',
                //             'moa'
                //         )
                //         ->where('email', $request->email)
                //         ->first();

                // Session::put('userId', $info->id);
                // $roleID = RoleUser::getRoleByUserID();
                // if($roleID==6) //expired trial
                // {                
                //     Auth::logout();             
                //     return redirect()->away(env('SURL').'/login?account=expired');                
                // }

                // //log ip
                // $ip_address = $_SERVER['REMOTE_ADDR'];
                // $ip_check = DB::table('user_ips')
                //             ->where('user_id',$info->id)
                //             ->where('ip_address',$ip_address)
                //             ->count();
                // if($ip_check==0)
                // {
                //     DB::table('user_ips')
                //     ->insert([
                //         'user_id'=>$info->id,
                //         'ip_address'=>$ip_address
                //     ]);
                // }
                

                // Session::put('verified', $info->verified);
                // Session::put('subtype', '');


                return redirect()->intended('/welcome');

            } else {

                Session::flash('message_danger', 'These credentials do not match our records. 004');
                return back()->withInput();

            }

        }else{

            return redirect()->away(env('SURL').'/signup');

        }
    }

}
