<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PopupEntireScreen extends Component
{
    /**
     * Title to kebab for popup id
     *
     * @var string
     */
    public $title;

    /**
     * Is the first content
     *
     * @var string
     */
    public $firstContent;

    /**
     * Popup content
     *
     * @var array
     */
    public $contents;

    /**
     * Is a form or content
     *
     * @var bool
     */
    public $isform;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($firstContent = 'Need first content', $title = 'Need a title', $contents = [], $isform = false)
    {
        $this->title = $title;
        $this->firstContent = $firstContent;
        $this->contents = $contents;
        $this->isform = $isform;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.popup-entire-screen');
    }
}
