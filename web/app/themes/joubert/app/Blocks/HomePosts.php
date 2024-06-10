<?php

namespace App\Blocks;

class HomePosts extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('HomePosts', "Bloc: Homepage Cartes d'articles", "Affiche un titre et des cartes d'articles juste en-dessous.", 'formatting', 'posts', 'admin-post', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
