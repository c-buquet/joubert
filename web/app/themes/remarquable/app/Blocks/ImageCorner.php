<?php

namespace App\Blocks;

class ImageCorner extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('ImageCorner', "Bloc: Image dans le coin", "Affiche une petite image dans le coin à droite (entre deux sections).", 'formatting', 'image corner', 'media-document', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
