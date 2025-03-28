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

    $page = get_page_by_path('don-ngoai-san');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Đơn ngoài sàn', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'don-ngoai-san', // Đường dẫn
        );
        wp_insert_post($new_page);
    }

    $page = get_page_by_path('don-thanh-toan-ho');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => ' Đơn thanh toán hộ', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'don-thanh-toan-ho', // Đường dẫn
        );
        wp_insert_post($new_page);
    }

    $page = get_page_by_path('chi-tiet-don-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Chi Tiết Đơn Hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'chi-tiet-don-hang', // Đường dẫn
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
    $page = get_page_by_path('nap-tien');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Nạp tiền', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'nap-tien', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('tai-khoan');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Tài khoản', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'tai-khoan', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('doi-mat-khau');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Đổi mật khẩu', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'doi-mat-khau', // Đường dẫn
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
        status TINYINT DEFAULT 1,
        note TEXT NULL,
        is_gia_co TINYINT DEFAULT 0,
        is_kiem_dem_hang TINYINT DEFAULT 0,
        is_bao_hiem TINYINT DEFAULT 0,
        type TINYINT DEFAULT 0, -- 0 -> order; 1 -> kí gửi
        ho_ten VARCHAR(500) NULL,
        address VARCHAR(500) NULL,
        email VARCHAR(500) NULL,
        phone VARCHAR(500) NULL,
        van_don VARCHAR(255) NULL,
        brand VARCHAR(255) NULL,
        thuong_hieu VARCHAR(255) NULL,
        so_kien_hang BIGINT(20) NULL,
        da_thanh_toan FLOAT(10,2) DEFAULT 0, -- Tổng tiền hàng
        da_hoan FLOAT(10,2) DEFAULT 0, -- Tổng tiền hoàn
        exchange_rate FLOAT(10,2) DEFAULT NULL,
        phi_mua_hang FLOAT(10,2) DEFAULT NULL,
        phi_ship_noi_dia FLOAT(10,2) NULL,
        phi_kiem_dem FLOAT(10,2) DEFAULT NULL,
        phi_gia_co FLOAT(10,2) DEFAULT NULL,
        chiet_khau_dich_vu FLOAT(10,2) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function update_orders_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'orders';

    $columns_to_add = [
        'link_san_pham' => "VARCHAR(255) NULL",
        'link_hinh_anh' => "VARCHAR(255) NULL",
        'mau_sac_kich_thuoc' => "VARCHAR(255) NULL",
        'ngay_dat_coc' => "DATETIME DEFAULT NULL",
        'da_mua_hang' => "DATETIME DEFAULT NULL",
        'ngay_nhap_kho_tq' => "DATETIME DEFAULT NULL",
        'ngay_nhap_kho_vn' => "DATETIME DEFAULT NULL",
        'ngay_nhan_hang' => "DATETIME DEFAULT NULL",
        'ngay_ncc_phat_hang' => "DATETIME DEFAULT NULL",
        'tien_van_chuyen' => "FLOAT(10,2) DEFAULT NULL",
        'kg_tinh_phi' => "FLOAT(10,2) DEFAULT NULL",
    ];

    foreach ($columns_to_add as $column => $definition) {
        $exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE '$column'");
        if (empty($exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN $column $definition");
        }
    }
}

update_orders_table();

function update_cart_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';

    $columns_to_add = [
        'size' => "VARCHAR(255) NULL",
        'color' => "VARCHAR(255) NULL",
    ];

    foreach ($columns_to_add as $column => $definition) {
        $exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE '$column'");
        if (empty($exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN $column $definition");
        }
    }
}

update_cart_table();

function create_orders_support()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'orders_support';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(500) NULL,
        trang_thai TINYINT DEFAULT 0,
        phi_dich_vu FLOAT(10,2) DEFAULT NULL,
        tong_tien FLOAT(10,2) DEFAULT NULL,
        ghi_chu TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function create_history_orders_transaction()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'history_orders_transaction';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(500) NULL,
        loai TEXT DEFAULT NULL,
        hinh_thuc TEXT DEFAULT NULL,
        so_tien FLOAT(10,2) DEFAULT NULL,
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

function create_chat_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'chat';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        order_id BIGINT(20) NOT NULL,
        is_system TINYINT DEFAULT 0,
        text TEXT NULL,
        added_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function create_wallet_transaction()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wallet_transaction';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
         id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        so_tien DECIMAL(15,2) NOT NULL,
        ma_phieu_thu VARCHAR(50) NOT NULL,
        ghi_chu TEXT NULL,
        hinh_anh VARCHAR(255) NULL,
        da_xu_ly TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function create_notification()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'notification';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        loai VARCHAR(255) NOT NULL,
        noi_dung TEXT NULL,
        du_lieu TEXT NULL,
        is_read TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function create_package_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'packages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id BIGINT(20) UNSIGNED NOT NULL,
        ma_kien VARCHAR(255) NOT NULL, -- Mã kiện
        can_nang FLOAT(10,2) DEFAULT 0.00, -- Cân nặng
        the_tich FLOAT(10,2) DEFAULT 0.00, -- Thể tích
        trang_thai TINYINT DEFAULT 0, -- Trạng thái
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    $wpdb->query($sql);
}

function after_setup_theme()
{
    create_muahang_page();
    create_orders_table();
    create_orders_support();
    create_cart_table();
    create_chat_table();
    create_wallet_transaction();
    create_notification();
    create_history_orders_transaction();
    create_package_table();
}

add_action('after_setup_theme', 'after_setup_theme');
