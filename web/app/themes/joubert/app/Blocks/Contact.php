<?php

namespace App\Blocks;

class Contact extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('Contact', "Bloc: Contact (image/boutons)", "Affiche une grande image avec un titre et deux boutons max.", 'formatting', 'contact', 'email-alt', 'preview', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
    }
}
