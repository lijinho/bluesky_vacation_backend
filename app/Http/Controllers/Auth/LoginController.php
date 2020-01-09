<?php

namespace App\Http\Controllers\Auth;

use App\Models\Front\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Crypt;
use Tymon\JWTAuth\Exceptions\JWTException;

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
        
    }

    public function Login(Request $request){
        $auth = false;
        $credentials = $request->json()->all();
        $jwt_token = null;
        if (!($jwt_token = auth()->attempt($credentials))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }
        
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            'userinfo' => $user
            ]);
    }
    protected function LoginSocial(Request $request)
    {
        $auth = false;
        $credentials = $request->json()->all();
        $user=User::where('email',$credentials['email'])->get();
        $credentials['password'] =$user[0]->real_pass;
        $jwt_token = null;
        if (!($jwt_token = auth()->attempt($credentials))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

        $user = auth()->user();
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            'userinfo' => $user
        ]);
    }
    
    protected function verifyUser(Request $request)
    {
        $credentials = $request->json()->all();
        $user=User::where('email',$credentials['email'])->update(['status'=>'active']);
        return response()->json([
            'success' => true]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        // dd($request);
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $user = $this->guard()->user();

        if($this->authenticated($request, $user)) {
            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return true;
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => trans('auth.failed')
        ], 422);
    }
}
