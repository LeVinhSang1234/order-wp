<?php

if (!is_admin()) {
    // Remove admin bar
    function show_admin_bar_for_admin_only()
    {
        if (!current_user_can('administrator')) add_filter('show_admin_bar', '__return_false');
    }
    add_action('wp', 'show_admin_bar_for_admin_only');
}

// Block user go to the admin page
function restrict_wp_admin_access()
{
    if (is_user_logged_in() && is_admin() && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'restrict_wp_admin_access');
