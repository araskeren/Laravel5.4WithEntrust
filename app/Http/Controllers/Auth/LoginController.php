<?php

namespace App\Http\Controllers\Auth;

use DB;
use Mail;
use Auth;
use Hash;
use Session;
use Socialite;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;

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

    public function login(Request $request){
    	$this->validate($request, [
            'username' => 'required', 'password' => 'required',
        ]);

    	$credentials = $this->credentials($request);
		if (Auth::validate($credentials)) {
            $user = Auth::getLastAttempted();
            if ($user->deleted_at == null) {
                /*if($user->session_id != ''){
                    if($user->ip_address != $request->ip()){
                        $msg = 'USER ANDA SENDANG DIGUNAKAN OLEH IP ADDRRES '.$user->ip_address.' IP ANDA ,'.$request->ip().' PADA WAKTU '.$user->last_login;

                        Session::flash("flash_notification", [
                                "level"=>"info",
                                "message"=> $msg
                        ]);
                        
                        return  redirect()
                        ->back()
                        ->withInput($request->only($this->username(), 'remember'));
                    }
                    
                }*/
                $update_user = User::find($user->id);
                $update_user->last_login = carbon::now();
                $update_user->ip_address = $request->ip();
                $update_user->session_id = Uuid::generate(1);
                $update_user->save();

                Auth::login($user, $request->has('remember'));
                return redirect()->intended($this->redirectPath());
                    
            } else if($user->deleted_at){
                Session::flash("flash_notification", [
                        "level"=>"info",
                        "message"=>"User sudah sudah tidak aktif lagi cuy"
                ]);
                
                return  redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'));
            }
        }

        return redirect('/login')
        ->withInput($request->only('username', 'remember'))
        ->withErrors([
            'username' => 'Incorrect Username/E-mail or password',
        ]);
    }

    public function username(){
        return 'username';
    }

    public function logout(Request $request) {
        $user = Auth::user();
        $user->session_id = null;
        $user->last_logout = carbon::now();
        $user->save();

        Auth::logout();
        return redirect('/login');
    }
}
