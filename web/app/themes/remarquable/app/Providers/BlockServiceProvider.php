<?php

namespace App\Providers;

use App\Blocks\Madlib;
use Roots\Acorn\ServiceProvider;

class BlockServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        (new Madlib());

    }
}
