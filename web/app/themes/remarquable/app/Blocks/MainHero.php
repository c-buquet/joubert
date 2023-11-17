<?php

namespace App\Blocks;

class MainHero extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('MainHero', "Bloc: Main Hero", "Affiche une grande image avec un pictogramme de souris indiquant qu'il est possible de scroll vers le bas.", 'formatting', 'main hero, image, header', 'cover-image', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
