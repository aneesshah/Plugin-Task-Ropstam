<?php

// Enqueue styles
function enqueue_form_styles() {
    wp_enqueue_style('website-form-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_form_styles');

// Display the form
function display_website_form() {
    // Check if the form has been submitted successfully
    if (isset($_GET['submission']) && $_GET['submission'] == 'success') {
        // Add a script to show the success message in a dialog box
        echo '<script>alert("Thank you for submitting your website!");</script>';
    }
    
    ?>
    <div class="website-form-container">
        <form method="post" action="">
            <label for="name">Your Name:</label>
            <input type="text" name="website_name" required><br>
            
            <label for="url">Website URL:</label>
            <input type="url" name="website_url" required><br>
            
            <input type="submit" value="Submit">
        </form>
    </div>
    <?php
}

add_shortcode('website_submission_form', 'display_website_form');

// Handle form submission
function handle_website_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['website_url'])) {
        $name = sanitize_text_field($_POST['website_name']);
        $url = esc_url_raw($_POST['website_url']);
        
        // Insert a new "WEBSITES" post
        $website_post_id = wp_insert_post(array(
            'post_title' => $name,
            'post_type' => 'websites',
            'post_status' => 'publish',
        ));
        
        // Save the URL as post meta
        if ($website_post_id) {
            update_post_meta($website_post_id, '_website_url', $url);
        }
        
        // Redirect after submission
        wp_redirect(add_query_arg('submission', 'success', get_permalink()));
        exit();
    }
}
add_action('template_redirect', 'handle_website_form_submission');
