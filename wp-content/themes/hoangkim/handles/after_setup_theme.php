<?php
function create_muahang_page()
{
    // Kiểm tra xem trang "mua-hang" đã tồn tại hay chưa
    $page = get_page_by_path('mua-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Mua Hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'mua-hang', // Đường dẫn
        );
        wp_insert_post($new_page);
    }

    $page = get_page_by_path('don-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Đơn Hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'don-hang', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('don-hang-ky-gui');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Đơn Hàng Ký Gửi', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'don-hang-ky-gui', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('wallet');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Ví điện tử', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'wallet', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('khieu-nai');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Khiếu nại', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'khieu-nai', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('gio-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Giỏ hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'gio-hang', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
}

function create_orders_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'orders';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        cart_ids VARCHAR(255) NOT NULL,
        exchange_rate FLOAT(10,2) NOT NULL,
        phi_mua_hang INT(11) DEFAULT 0,
        status TINYINT DEFAULT 1,
        note TEXT NULL,
        is_gia_co TINYINT DEFAULT 0,
        is_kiem_dem_hang TINYINT DEFAULT 0,
        is_bao_hiem TINYINT DEFAULT 0
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function create_cart_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        shop_url VARCHAR(500) NULL,
        shop_id VARCHAR(255) NULL,
        product_name VARCHAR(255) NULL,
        product_image TEXT NOT NULL,
        product_url TEXT NOT NULL,
        product_note TEXT NULL,
        web VARCHAR(50) NULL,
        quantity INT(11) NOT NULL DEFAULT 1,
        price FLOAT(10,2) NOT NULL,
        added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_done TINYINT DEFAULT 0,
        is_select TINYINT DEFAULT 0
    ) $charset_collate;";
    $wpdb->query($sql);
}

function after_setup_theme()
{
    create_muahang_page();
    create_orders_table();
    create_cart_table();
}

add_action('after_setup_theme', 'after_setup_theme');
