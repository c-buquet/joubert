<?php

namespace App\Blocks;

class Madlib extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('Madlib', "Madlib", "Ajoute le madlib", 'formatting', '', 'fullscreen-alt', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
