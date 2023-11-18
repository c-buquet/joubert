<?php

namespace App\Providers;

use App\Blocks\MainHero;
use App\Blocks\Contact;
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
        (new MainHero());
        (new Contact());
    }
}
