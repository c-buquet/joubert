<?php

namespace App\View\Components\Icons;

use Illuminate\View\Component;

class HorizontalBar extends Component
{

    public $color;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $color = 'bg-green-primary')
    {
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.icons.horizontal-bar');
    }
}
