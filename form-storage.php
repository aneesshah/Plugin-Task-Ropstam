<?php
/*
Plugin Name: Ropstam Plugin Task
Description: A plugin that stores website URLs submitted through a form as custom post types.
Version: 1.0
Author: Syed Anees Zia
*/

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/frontend-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/backend.php';

// Register the custom post type "WEBSITES"
function register_website_cpt() {
    $labels = array(
        'name' => 'Websites',
        'singular_name' => 'Website',
        'menu_name' => 'Websites',
        'all_items' => 'All Websites',
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'), // We will hide this in the edit screen
        'show_ui' => true,
        'show_in_menu' => true,
    );
    
    register_post_type('websites', $args);
}
add_action('init', 'register_website_cpt');
