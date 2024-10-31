<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Regency;
use App\Models\CarModel;
use App\Models\CarType;
use App\Models\CarColor;
use App\Models\User;
class SessionContrroller extends Controller
{
    public function index ()
    {
        return view ( 'login' );
    }

    public function login ( Request $request )
    {
        $credentials = $request->validate ( [ 'username' => 'required', 'password' => 'required', 'captcha' => 'required|captcha' ] );
        if ( Auth::attempt ( [ 'username' => $credentials[ 'username' ], 'password' => $credentials[ 'password' ] ] ) )
        {
            if ( $request->session ()->regenerate () )
            {
                return redirect ()->intended ( 'dashboard' )->with ( 'success', 'Welcome ' . Auth::user ()->name );

            }
            return back ()->with ( 'loginError', 'Login Failed!' )->with ( 'error', 'Username or Password is incorrect' );

        }
        return back ()->with ( 'loginError', 'Login Failed!' )->with ( 'error', 'Username or Password is incorrect' );
    }
    public function logout ( Request $request )
    {
        Auth::logout ();
        $request->session ()->invalidate ();
        $request->session ()->regenerateToken ();
        return redirect ( '/' );
    }
    public function reloadCaptcha ()
    {
        return response ()->json ( [ 'captcha' => captcha_img ( 'math' ) ] );
    }
}