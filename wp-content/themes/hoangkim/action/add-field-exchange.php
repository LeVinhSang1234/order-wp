<?php
/*
Plugin Name: Exchange Rate Config
Description: Thêm field cấu hình tỷ giá vào trang quản trị WordPress.
Version: 1.0
Author: Your Name
*/

// Đăng ký setting và field tỷ giá vào trang General Settings
function erc_add_exchange_rate_setting()
{
    // Đăng ký setting mới
    register_setting(
        'general', // Nhóm settings (chúng ta thêm vào phần General Settings)
        'exchange_rate', // Tên option lưu tỷ giá
        array(
            'type' => 'float',
            'description' => 'Tỷ giá hiện tại',
            'sanitize_callback' => 'floatval', // Hàm lọc dữ liệu đầu vào
            'default' => 1.0, // Giá trị mặc định
        )
    );

    // Thêm field vào phần General Settings
    add_settings_field(
        'exchange_rate_field', // ID của field
        'Tỷ giá hiện tại', // Tiêu đề field
        'erc_display_exchange_rate_field', // Hàm hiển thị field
        'general', // Trang settings (General Settings)
        'default', // Section chứa field
        array('label_for' => 'exchange_rate') // Tham số bổ sung
    );
}
add_action('admin_init', 'erc_add_exchange_rate_setting');

// Hàm hiển thị trường nhập liệu tỷ giá
function erc_display_exchange_rate_field()
{
    // Lấy giá trị tỷ giá đã lưu từ option
    $exchange_rate = get_option('exchange_rate', 1.0);
    echo '<input type="number" name="exchange_rate" value="' . esc_attr($exchange_rate) . '" step="0.01" min="0" />';
}

// Đăng ký setting và field giá tiền theo cân nặng vào trang General Settings
function erc_add_price_per_kg_setting()
{
    // Đăng ký setting mới
    register_setting(
        'general', // Nhóm settings (General Settings)
        'price_per_kg', // Tên option lưu giá tiền theo cân nặng
        array(
            'type' => 'float',
            'description' => 'Giá tiền theo cân nặng (VNĐ/kg)',
            'sanitize_callback' => 'floatval', // Hàm lọc dữ liệu đầu vào
            'default' => 0.0, // Giá trị mặc định
        )
    );

    // Thêm field vào phần General Settings
    add_settings_field(
        'price_per_kg_field', // ID của field
        'Giá tiền theo cân nặng (VNĐ/kg)', // Tiêu đề field
        'erc_display_price_per_kg_field', // Hàm hiển thị field
        'general', // Trang settings (General Settings)
        'default', // Section chứa field
        array('label_for' => 'price_per_kg') // Tham số bổ sung
    );
}
add_action('admin_init', 'erc_add_price_per_kg_setting');

// Hàm hiển thị trường nhập liệu giá tiền theo cân nặng
function erc_display_price_per_kg_field()
{
    // Lấy giá trị đã lưu từ option
    $price_per_kg = get_option('price_per_kg', 0.0);
    echo '<input type="number" name="price_per_kg" value="' . esc_attr($price_per_kg) . '" step="0.01" min="0" />';
}

// Thêm plugin vào trong Admin Settings
function erc_plugin_menu()
{
    // Tạo menu plugin trong phần Cài đặt
    add_options_page(
        'Cấu hình tỷ giá', // Tiêu đề của trang
        'Tỷ giá hiện tại', // Tên menu
        'manage_options', // Quyền truy cập
        'exchange_rate_config', // Slug của menu
        'erc_plugin_page' // Hàm hiển thị trang plugin
    );
}
add_action('admin_menu', 'erc_plugin_menu');

// Hàm hiển thị trang cấu hình của plugin
function erc_plugin_page()
{
?>
    <div class="wrap">
        <h1>Cấu hình tỷ giá</h1>
        <form method="post" action="options.php">
            <?php
            // Cung cấp thông tin cấu hình và field tỷ giá
            settings_fields('general');
            do_settings_sections('general');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Đăng ký API GET endpoint
function erc_register_exchange_rate_api()
{
    add_action('wp_ajax_get_exchange_rate', 'erc_get_exchange_rate');
    add_action('wp_ajax_nopriv_get_exchange_rate', 'erc_get_exchange_rate'); // Cho phép không đăng nhập
}
add_action('init', 'erc_register_exchange_rate_api');

function erc_get_exchange_rate()
{
    $exchange_rate = get_option('exchange_rate', 1.0); // Nếu chưa có tỷ giá, mặc định là 1.0
    wp_send_json_success($exchange_rate);
}
