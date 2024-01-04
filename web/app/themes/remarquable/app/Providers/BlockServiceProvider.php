<?php

namespace App\Providers;

use App\Blocks\MainHero;
use App\Blocks\Contact;
use App\Blocks\SpacerBlock;
use App\Blocks\SimpleImage;
use App\Blocks\WhyChooseUs;
use App\Blocks\Presentation;
use App\Blocks\ImageCorner;
use App\Blocks\OurExperts;
use App\Blocks\OurSolutionSlider;
use App\Blocks\HomePosts;
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
        (new SimpleImage());
        (new WhyChooseUs());
        (new Presentation());
        (new ImageCorner());
        (new OurExperts());
        (new OurSolutionSlider());
        (new HomePosts());
    }
}
