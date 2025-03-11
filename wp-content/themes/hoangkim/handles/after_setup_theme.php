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
        da_thanh_toan VARCHAR(500) NULL, -- Tổng tiền hàng
        phi_mua_hang FLOAT(10,2) DEFAULT 0,
        phi_bao_hiem FLOAT(10,2) DEFAULT 0,
        phi_kiem_dem FLOAT(10,2) DEFAULT 0,
        phi_gia_co FLOAT(10,2) DEFAULT 0,
        chiet_khau_dich_vu FLOAT(10,2) DEFAULT 0,
        phi_ship_noi_dia_trung FLOAT(10,2) DEFAULT 0,
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


// Đăng ký action để xử lý yêu cầu tạo đơn hàng
add_action('wp_ajax_create_order', 'create_order_via_ajax');
add_action('wp_ajax_nopriv_create_order', 'create_order_via_ajax'); // Xử lý cho người dùng chưa đăng nhập

// Hàm xử lý tạo đơn hàng qua AJAX
function create_order_via_ajax()
{
    // Kiểm tra nonce bảo mật
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_order_nonce')) {
        wp_send_json_error(['message' => 'Nonce không hợp lệ']);
        exit;
    }
    $shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : 0;
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Bạn cần đăng nhập để tạo đơn hàng.']);
        exit;
    }
    global $wpdb;
    $cart_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}cart WHERE shop_id = %d AND user_id = %d AND is_select = 1",
        $shop_id,
        $user_id
    ));
    if (empty($cart_ids)) {
        wp_send_json_error(['message' => 'Không tìm thấy giỏ hàng cho cửa hàng này.']);
        exit;
    }
    if (empty($cart_ids)) {
        wp_send_json_error(['message' => 'Không tìm thấy giỏ hàng cho cửa hàng này.']);
        exit;
    }
    $cart_ids_str = json_encode($cart_ids);
    $exchange_rate = floatval(get_option('exchange_rate', 1.0));
    $phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));
    $note = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';

    $ho_ten = isset($_POST['ho_ten']) ? sanitize_textarea_field($_POST['ho_ten']) : '';
    $address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
    $email = isset($_POST['email']) ? sanitize_textarea_field($_POST['email']) : '';
    $phone = isset($_POST['nophonete']) ? sanitize_textarea_field($_POST['phone']) : '';
    $is_gia_co = isset($_POST['is_gia_co']) ? intval($_POST['is_gia_co']) : 0;
    $is_kiem_dem_hang = isset($_POST['is_kiem_dem_hang']) ? intval($_POST['is_kiem_dem_hang']) : 0;
    $is_bao_hiem = isset($_POST['is_bao_hiem']) ? intval($_POST['is_bao_hiem']) : 0;
    $table = $wpdb->prefix . 'orders';
    $data = [
        'user_id' => $user_id,
        'cart_ids' => $cart_ids_str,
        'exchange_rate' => $exchange_rate,
        'phi_mua_hang' => $phi_mua_hang,
        'note' => $note,
        'is_gia_co' => $is_gia_co,
        'is_kiem_dem_hang' => $is_kiem_dem_hang,
        'is_bao_hiem' => $is_bao_hiem,
        'ho_ten' => $ho_ten,
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
    ];
    $format = [
        '%d',
        '%s',
        '%f',
        '%f',
        '%s',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s'
    ];
    $result = $wpdb->insert($table, $data, $format);
    if ($result !== false) {
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}cart SET is_done = 1 WHERE id IN (" . implode(',', array_fill(0, count($cart_ids), '%d')) . ")",
                ...$cart_ids
            )
        );
        wp_send_json_success(['message' => 'Đơn hàng đã được tạo thành công.']);
    } else {
        wp_send_json_error(['message' => 'Lỗi khi tạo đơn hàng.']);
    }
    exit;
}
