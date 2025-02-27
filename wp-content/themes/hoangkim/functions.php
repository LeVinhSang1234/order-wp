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
        $steps = array(
            array('title' => 'theme-style', 'name' => "/style.css"),
            array('title' => 'header-style', 'name' => "/css/header.css"),
            array('title' => 'footer-style', 'name' => "/css/footer.css"),
            array('title' => 'banner-style', 'name' => "/css/banner.css"),
            array('title' => 'step-style', 'name' => "/css/step.css"),
            array('title' => 'group-order-style', 'name' => "/css/group-order.css"),
            array('title' => 'bang-gia-style', 'name' => "/css/bang-gia.css"),
        );
        foreach ($steps as $step) {
            $css_file = get_template_directory() . $step['name'];
            $css_version = filemtime($css_file);
            wp_enqueue_style($step['title'], get_template_directory_uri() . $step['name'], array(), $css_version);
        }
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

    function load_custom_admin_css()
    {
        wp_enqueue_style('theme-style', get_template_directory_uri() . '/style.css');
    }
    add_action('admin_enqueue_scripts', 'load_custom_admin_css');

    require_once get_template_directory() . '/custom-ui/add-custom-logo.php';
    require_once get_template_directory() . '/custom-ui/add-phone.php';
}

if (!is_admin()) {
    require_once get_template_directory() . '/short-code/banner.php';
    require_once get_template_directory() . '/short-code/step.php';
    require_once get_template_directory() . '/short-code/group-order.php';
    require_once get_template_directory() . '/short-code/bang-gia.php';
    require_once get_template_directory() . '/short-code/login.php';
    require_once get_template_directory() . '/short-code/dang-ki.php';
}
