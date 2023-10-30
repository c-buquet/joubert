<?php
//Add shortcodes for buttons and links
function btn_shortcode($atts) {
    $args = shortcode_atts( array(
       'texte' => 'En savoir plus',
       'type' => 'primary',
       'couleur' => 'orange',
   ), $atts );
   return Blade::render('<x-button color="'.$args['couleur'].'" type="'.$args['type'].'">'. $args['texte'] .'</x-button>');
}
add_shortcode('btn', 'btn_shortcode');

//Add shortcodes for buttons and links
function text_chevron_shortcode($atts) {
    $args = shortcode_atts( array(
       'texte' => 'En savoir plus',
       'type' => 'primary',
       'chevron' => 'chevron',
   ), $atts );
   return Blade::render('<x-text id="shortcode-link" type="'.$args['type'].'" icon="'.$args['chevron'].'">'. $args['texte'] .'</x-text>');
}
add_shortcode('text-chevron', 'text_chevron_shortcode');
