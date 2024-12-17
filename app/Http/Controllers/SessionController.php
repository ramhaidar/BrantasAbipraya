<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function index ()
    {
        return view ( 'login' );
    }

    public function login ( Request $request )
    {
        $messages = [ 
            'cf-turnstile-response.required'  => 'Mohon selesaikan verifikasi CAPTCHA',
            'cf-turnstile-response.turnstile' => 'Verifikasi CAPTCHA tidak valid. Silakan coba lagi.',
        ];

        $credentials = $request->validate ( [ 
            'username' => 'required',
            'password' => 'required',
            // 'cf-turnstile-response' => [ 'required', Rule::turnstile () ],
        ], $messages );

        if (
            Auth::attempt ( [ 
                'username' => $credentials[ 'username' ],
                'password' => $credentials[ 'password' ]
            ] )
        )
        {
            $request->session ()->regenerate ();

            return redirect ( 'dashboard' )
                ->with ( 'success', 'Welcome ' . Auth::user ()->name );
        }

        return back ()
            ->with ( 'loginError', 'Login Failed!' )
            ->with ( 'error', 'Username or Password is incorrect' );
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