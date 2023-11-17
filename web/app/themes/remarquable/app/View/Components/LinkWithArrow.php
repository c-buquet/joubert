<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LinkWithArrow extends Component
{
    /**
     * Titre du lien
     *
     * @var string
     */
    public $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = 'Need a title')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.link-with-arrow');
    }
}
