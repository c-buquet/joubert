<?php

namespace App\Blocks;

class SimpleImage extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('SimpleImage', "Bloc: Simple Image (Full size)", "Affiche une grande image prennant toute la largeur de l'écran comme taille. Vous pouvez décider de la direction du background", 'formatting', 'image, simple image', 'cover-image', 'auto', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
