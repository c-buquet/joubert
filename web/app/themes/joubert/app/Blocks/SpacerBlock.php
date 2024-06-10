<?php

namespace App\Blocks;

class SpacerBlock extends BaseBlock
{
    public function __construct()
    {
        parent::__construct('SpacerBlock', __('Block: Spacer block', 'neoen_theme'), __('Block allowing to add specific spaces according to the devices', 'neoen_theme'), 'formatting', '', 'fullscreen-alt', 'auto', false, false);
    }

    public function setContext($context): void
    {
        $this->context['fields'] = get_fields();
        $this->context['spacings'] = $this->getSpacingClasses(get_field('spacings'));
    }


    private function getSpacingClasses($spacings): string
    {
        $class = "";

        foreach ($spacings as $spacing) {
            if ($spacing['device'] === "null") {
                $class .= 'pt-' . $spacing['value'] . " ";
            } else {
                $class .= $spacing['device'] . ":pt-" . $spacing['value'] . " ";
            }
        }

        return $class;
    }
}
