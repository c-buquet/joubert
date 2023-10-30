<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

try {
    \Roots\bootloader();
} catch (Throwable $e) {
    wp_die(
        __('You need to install Acorn to use this theme.', 'sage'),
        '',
        [
            'link_url' => 'https://docs.roots.io/acorn/2.x/installation/',
            'link_text' => __('Acorn Docs: Installation', 'sage'),
        ]
    );
}

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });

/*
|--------------------------------------------------------------------------
| Enable Sage Theme Support
|--------------------------------------------------------------------------
|
| Once our theme files are registered and available for use, we are almost
| ready to boot our application. But first, we need to signal to Acorn
| that we will need to initialize the necessary service providers built in
| for Sage when booting.
|
*/

add_theme_support('sage');

//Add colors to the wysiwyg
function my_mce4_options($init) {

    $custom_colours = '
        "F6F6FA", "Identitaires Bleu",
        "F97B3D", "Identitaires Orange",
        "383838", "Identitaires Black",
        "2DAAAF", "Commerciales Turquoise",
        "FF9B9B", "Commerciales Rose",
        "007DAF", "Commerciales Bleu",
        "FA696E", "Commerciales Rouge",
        "FFAA5F", "Commerciales Orange",
        "B9B41E", "Commerciales Vert",
        "F6F6FA", "Grey 200",
        "EDF1F4", "Grey 300",
        "D6DCDF", "Grey 400",
        "8C98A3", "Grey 500",
        "5C656B", "Grey 600",
        "2C3438", "Grey 700",
        "13191D", "Grey 800",
    ';

    // build colour grid default+custom colors
    $init['textcolor_map'] = '['.$custom_colours.']';

    // change the number of rows in the grid if the number of colors changes
    // 8 swatches per row
    $init['textcolor_rows'] = 4;

    return $init;
}
add_filter('tiny_mce_before_init', 'my_mce4_options');

require 'shortcodes.php';

function prefix_register_script( $scripts ) {

    $scripts[] = [
        'handle'  => 'blog-gridbuilder',
        'source'  => get_stylesheet_directory_uri() . "/resources/scripts/blog-gridbuilder.js",
        'version' => '1.0.0',
    ];

    return $scripts;

}

add_filter( 'wp_grid_builder/frontend/register_scripts', 'prefix_register_script' );
