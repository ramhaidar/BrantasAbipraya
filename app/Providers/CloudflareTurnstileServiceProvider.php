<?php
// app/Providers/CloudflareTurnstileServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CloudflareTurnstileServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register () : void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot () : void
    {
        // Definisi validator untuk turnstile
        Validator::extend ( 'turnstile', function ($attribute, $value, $parameters)
        {
            if ( config ( 'services.cloudflare.dev_mode' ) )
            {
                return true;
            }

            try
            {
                $response = Http::asForm ()->post ( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [ 
                    'secret'   => config ( 'services.cloudflare.secret_key' ),
                    'response' => $value,
                    'remoteip' => request ()->ip (),
                ] );

                return $response->successful () && $response->json ( 'success' );
            }
            catch ( \Exception $e )
            {
                return false;
            }
        } );
    }
}