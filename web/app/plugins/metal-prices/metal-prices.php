<?php
/*
Plugin Name: Metal Prices
Description: A plugin to display metal prices and historical data.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the necessary files
include_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Register the shortcode
function register_metal_prices_shortcode() {
    add_shortcode('metal_prices', 'metal_prices_shortcode');
}
add_action('init', 'register_metal_prices_shortcode');

// Register the admin settings page
function register_metal_prices_settings_page() {
    add_menu_page(
        'Metal Prices Settings',
        'Metal Prices',
        'manage_options',
        'metal-prices',
        'metal_prices_settings_page_html'
    );
}
add_action('admin_menu', 'register_metal_prices_settings_page');

// Enqueue le fichier CSS pour le shortcode
function enqueue_metal_prices_styles() {
    wp_enqueue_style('metal-prices-styles', plugin_dir_url(__FILE__) . 'assets/styles/index.css');
}
add_action('wp_enqueue_scripts', 'enqueue_metal_prices_styles');