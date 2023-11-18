<?php

namespace App\Blocks;

class WhyChooseUs extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('WhyChooseUs', "Bloc: Why choose us (4 cards)", "Affiche un titre et des petites cartes en ligne", 'formatting', 'why choose us, cards', 'embed-generic', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
