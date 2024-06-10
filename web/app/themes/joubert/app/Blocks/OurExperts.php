<?php

namespace App\Blocks;

class OurExperts extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('OurExperts', "Bloc: Our Experts (Big cards)", "Affiche un titre et son texte ainsi que des grande cartes", 'formatting', 'our experts, big cards, cards', 'welcome-widgets-menus', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
