<?php
function mytheme_setup()
{
    add_theme_support('title-tag'); // Hỗ trợ tự động thêm thẻ <title>
    add_theme_support('post-thumbnails'); // Hỗ trợ ảnh đại diện
    register_nav_menus([
        'main_menu' => __('Main Menu', 'hoangkim'),
    ]);

    function mytheme_enqueue_scripts()
    {
        $css_file = get_template_directory() . '/style.css';
        $css_version = filemtime($css_file);
        wp_enqueue_style('theme-style', get_template_directory_uri() . '/style.css', array(), $css_version);

        $css_file_header = get_template_directory() . '/css/header.css';
        $css_version_header = filemtime($css_file_header);
        wp_enqueue_style('header-style', get_template_directory_uri() . '/css/header.css', array(), $css_version_header);

        $css_file_banner = get_template_directory() . '/css/banner.css';
        $css_version_banner = filemtime($css_file_banner);
        wp_enqueue_style('banner-style', get_template_directory_uri() . '/css/banner.css', array(), $css_version_banner);
    }
    add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts', 100);
}

function remove_head()
{
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
}

if (!is_admin()) {
    add_action('init', 'remove_head');
}
add_action('after_setup_theme', 'mytheme_setup');


if (is_admin()) {
    require_once get_template_directory() . '/custom-ui/add-custom-logo.php';
    require_once get_template_directory() . '/custom-ui/add-phone.php';
}

require_once get_template_directory() . '/short-code/banner.php';
