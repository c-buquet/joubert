<?php

namespace App\Providers;

use App\Blocks\MainHero;
use App\Blocks\Contact;
use App\Blocks\SpacerBlock;
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
        (new SpacerBlock());
    }
}
