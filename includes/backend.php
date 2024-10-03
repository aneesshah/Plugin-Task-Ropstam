<?php

// Disable the ability to create new websites from the admin interface
function disable_website_creation() {
    global $submenu;
    if (isset($submenu['edit.php?post_type=websites'])) {
        unset($submenu['edit.php?post_type=websites'][10]); // Removes 'Add New'
    }
}
add_action('admin_menu', 'disable_website_creation');

// Remove all metaboxes from the "WEBSITES" post type edit screen
function remove_all_metaboxes() {
    remove_meta_box('submitdiv', 'websites', 'side'); // Remove Publish box
    remove_meta_box('slugdiv', 'websites', 'normal'); // Remove Slug box
}
add_action('do_meta_boxes', 'remove_all_metaboxes');

// Add a custom meta box to display the source code of the website URL
function add_website_meta_box() {
    add_meta_box('website_source_code', 'Website Source Code', 'website_source_code_meta_box', 'websites', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_website_meta_box');

// Meta box callback function
function website_source_code_meta_box($post) {
    $url = get_post_meta($post->ID, '_website_url', true);
    
    if (current_user_can('administrator')) {
        if (!empty($url)) {
            $response = wp_remote_get($url);
            if (is_wp_error($response)) {
                echo '<p>Could not retrieve source code from URL.</p>';
            } else {
                $source_code = wp_remote_retrieve_body($response);
                echo '<textarea readonly style="width:100%; height:300px;">' . esc_textarea($source_code) . '</textarea>';
            }
        } else {
            echo '<p>No URL provided.</p>';
        }
    } else {
        // If not administrator, show only the name
        echo '<p>' . esc_html(get_the_title($post->ID)) . '</p>';
    }
}

// Restrict access to the "WEBSITES" custom post type access
function restrict_website_post_type_access() {
    if (!current_user_can('administrator')) {
        remove_menu_page('edit.php?post_type=websites'); // Hides 'Websites' from the menu
    }
}
add_action('admin_menu', 'restrict_website_post_type_access');

// Add custom columns for WEBSITES
function set_custom_edit_website_columns($columns) {
    $columns['website_url'] = __('Website URL', 'your_text_domain');
    return $columns;
}
add_filter('manage_websites_posts_columns', 'set_custom_edit_website_columns');

// Populate the custom columns
function custom_website_column($column, $post_id) {
    if ($column === 'website_url') {
        $url = get_post_meta($post_id, '_website_url', true);
        echo esc_html($url);
    }
}
add_action('manage_websites_posts_custom_column', 'custom_website_column', 10, 2);
