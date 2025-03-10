<?php
/*
Plugin Name: Phí mua hàng Config
Description: Thêm field cấu hình tỷ giá vào trang quản trị WordPress.
Version: 1.0
Author: Your Name
*/

function erc_add_phi_mua_hang_setting()
{
    register_setting(
        'general', 
        'phi_mua_hang',
        array(
            'type' => 'float',
            'description' => 'Phí mua hàng (%)',
            'sanitize_callback' => 'floatval', 
            'default' => 0,
        )
    );

    add_settings_field(
        'phi_mua_hang_field', // ID của field
        'Phí mua hàng (%)', // Tiêu đề field
        'erc_display_phi_mua_hang_field', // Hàm hiển thị field
        'general', // Trang settings (General Settings)
        'default', // Section chứa field
        array('label_for' => 'phi_mua_hang') // Tham số bổ sung
    );
}
add_action('admin_init', 'erc_add_phi_mua_hang_setting');

// Hàm hiển thị trường nhập liệu tỷ giá
function erc_display_phi_mua_hang_field()
{
    $phi_mua_hang = get_option('phi_mua_hang', 0);
    echo '<input type="number" name="phi_mua_hang" value="' . esc_attr($phi_mua_hang) . '" step="0.01" min="0" />';
}

function erc_register_phi_mua_hang_api()
{
    add_action('wp_ajax_get_phi_mua_hang', 'erc_get_phi_mua_hang');
    add_action('wp_ajax_nopriv_get_phi_mua_hang', 'erc_get_phi_mua_hang'); // Cho phép không đăng nhập
}
add_action('init', 'erc_register_phi_mua_hang_api');

function erc_get_phi_mua_hang()
{
    $phi_mua_hang = get_option('phi_mua_hang', 0); // Nếu chưa có tỷ giá, mặc định là 1.0
    wp_send_json_success($phi_mua_hang);
}
