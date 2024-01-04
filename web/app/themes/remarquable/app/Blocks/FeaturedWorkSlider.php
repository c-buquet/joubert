<?php

namespace App\Blocks;

class FeaturedWorkSlider extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('FeaturedWorkSlider', "Bloc: Featured Work (Slider)", "Affiche un titre sur un fond d'image principale à gauche, et un slider à droite avec des statistiques, des diagrammes et un tableau.", 'formatting', 'slider, slides, featured work', 'slides', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
