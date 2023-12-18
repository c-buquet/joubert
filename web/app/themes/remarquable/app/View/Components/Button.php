<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $type;

    public $icon;

    public $color;

    public $inverse;

    /**
     * The button types.
     *
     * @var array
     */
    public $classes = [
        'primary'   => [
            'green' => 'font-lato font-bold uppercase px-6 py-4 w-full lg:w-max tracking-wider text-green-primary hover:text-green-light flex items-center justify-center gap-x-3 bg-green-light border-2 hover:bg-green-primary border-green-light hover:border-green-primary duration-700',
        ],
        'secondary' => [
            'white'   => 'font-lato font-bold uppercase px-6 py-4 w-full lg:w-max tracking-wider text-white-cloud hover:text-green-light flex items-center justify-center gap-x-3 bg-transparent border-2 border-white-cloud hover:border-green-light duration-700',
        ]
    ];


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($icon = false, $type = 'primary', $color = 'green', $inverse = false)
    {
        $this->type = $this->classes[$type][$color] ?? $this->classes['primary']['green'];
        $this->icon = $icon;
        $this->inverse = $inverse;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
