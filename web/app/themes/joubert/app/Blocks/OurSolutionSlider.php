<?php

namespace App\Blocks;

class OurSolutionSlider extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('OurSolutionSlider', "Bloc: Our solution (Slider)", "Affiche un titre sur fond verts à gauche et un slider à droite.", 'formatting', 'slider, slides, our solution', 'slides', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
