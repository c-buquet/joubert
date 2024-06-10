<?php

namespace App\Blocks;

class Presentation extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('Presentation', "Bloc: Présentation (image/text)", "Affiche une image d'un côté et du contenu de l'autre.", 'formatting', 'presentation, image, texte, content', 'align-pull-left', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
