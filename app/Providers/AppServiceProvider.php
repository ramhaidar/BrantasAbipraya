<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        //
        Carbon::setLocale ( 'id' );
        //

        if ( request ()->secure () )
        {
            URL::forceScheme ( 'https' );
        }

        if ( config ( 'IS_FORCE_HTTPS' ) === true )
        {
            dd ( "TEST" );
            URL::forceScheme ( 'https' );
        }
    }
}
