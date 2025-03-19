<?php
function mytheme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
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
add_action('after_setup_theme', 'mytheme_setup');

function insert_notification($loai, $noi_dung, $du_lieu)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Bạn cần đăng nhập để tạo đơn hàng.']);
        exit;
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'notification';

    $wpdb->insert(
        $table_name,
        array(
            'user_id'   => $user_id,
            'loai'      => sanitize_text_field($loai),
            'noi_dung'  => $noi_dung ? sanitize_textarea_field($noi_dung) : null,
            'du_lieu'  => $du_lieu ? json_encode($du_lieu) : null,
        ),
        array('%d', '%s', '%s', '%s', '%d')
    );

    return $wpdb->insert_id; // Trả về ID của bản ghi vừa tạo
}

if (is_admin()) {
    function load_custom_admin_css()
    {
        wp_enqueue_style('theme-style', get_template_directory_uri() . '/style.css');
    }
    add_action('admin_enqueue_scripts', 'load_custom_admin_css');
    require_once get_template_directory() . '/custom-ui/add-custom-logo.php';
    require_once get_template_directory() . '/custom-ui/add-phone.php';
    require_once get_template_directory() . '/handles/after_setup_theme.php';
    require_once get_template_directory() . '/api/order.php';
    require_once get_template_directory() . '/action/add-field-exchange.php';
    require_once get_template_directory() . '/action/phi-mua-hang.php';
    require_once get_template_directory() . '/handles/custom_ui_admin.php';
    require_once get_template_directory() . '/handles/render_order_detail.php';
    require_once get_template_directory() . '/handles/render_transaction.php';
    require_once get_template_directory() . '/handles/render_order_support.php';
}

if (!is_admin()) {
    require_once get_template_directory() . '/short-code/banner.php';
    require_once get_template_directory() . '/short-code/step.php';
    require_once get_template_directory() . '/short-code/group-order.php';
    require_once get_template_directory() . '/short-code/bang-gia.php';
    require_once get_template_directory() . '/short-code/login.php';
    require_once get_template_directory() . '/short-code/dang-ki.php';
    require_once get_template_directory() . '/short-code/bai-viet-noi-bat.php';
    require_once get_template_directory() . '/short-code/dang-ki-bao-gia.php';
    require_once get_template_directory() . '/short-code/quy-trinh-nhap-hang.php';
}
require_once get_template_directory() . '/action/remove-head.php';
require_once get_template_directory() . '/action/bar-admin.php';
require_once get_template_directory() . '/api/dang-ki.php';
require_once get_template_directory() . '/custom-ui/add-user-code.php';
require_once get_template_directory() . '/api/order.php';
require_once get_template_directory() . '/api/exchange-rate.php';


function format_price_vnd($price)
{
    return number_format($price, 0, ',', '.') . ' ₫';
}
