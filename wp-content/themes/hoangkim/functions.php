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

add_action('wp_ajax_submit_all_cart', 'submit_all_cart_handler');
add_action('wp_ajax_nopriv_submit_all_cart', 'submit_all_cart_handler');


function submit_all_cart_handler()
{
    global $wpdb;
    $table_cart = $wpdb->prefix . 'cart';
    $table_orders = $wpdb->prefix . 'orders';

    $user_id = get_current_user_id();
    $data = json_decode(file_get_contents("php://input"), true);
    $ho_ten  = $data['ho_ten'] ?? '';
    $address = $data['address'] ?? '';
    $email   = $data['email'] ?? '';
    $phone   = $data['phone'] ?? '';
    $products_input = $data['products'] ?? []; 

    $products = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_cart WHERE user_id = %d AND is_done = 0 ORDER BY added_at DESC", $user_id),
        ARRAY_A
    );

    if (empty($products)) {
        wp_send_json_error(['message' => 'Không có sản phẩm nào được chọn!']);
        return;
    }

    $cart_ids = [];
    $total_price = 0;

    foreach ($products as &$product) {
        $product_id = $product['id'];

        // Tìm quantity được gửi lên từ request
        $new_quantity = 1; // Giá trị mặc định
        foreach ($products_input as $input_product) {
            if ($input_product['id'] == $product_id) {
                $new_quantity = intval($input_product['quantity']);
                break;
            }
        }

        // Cập nhật quantity vào product
        $product['quantity'] = $new_quantity;

        // Cập nhật database
        $wpdb->update(
            $table_cart,
            ['is_done' => 1, 'quantity' => $new_quantity],  // Cập nhật thêm quantity
            ['id' => $product_id, 'user_id' => $user_id],
            ['%d', '%d'], // Format cho `is_done` và `quantity`
            ['%d', '%d'] // Format cho `id` và `user_id`
        );

        // Tính tổng tiền
        $cart_ids[] = (string) $product_id;
        $total_price += floatval($product['price']) * $new_quantity;
    }


    // Tính phí dịch vụ dựa trên tổng giá
    if ($total_price < 5000000) {
        $service_fee = $total_price * 0.03; // 3%
    } elseif ($total_price >= 5000000 && $total_price <= 50000000) {
        $service_fee = $total_price * 0.02; // 2%
    } else {
        $service_fee = $total_price * 0.015; // 1.5%
    }

    // Chuyển cart_ids thành JSON để lưu vào đơn hàng
    $cart_ids_json = json_encode($cart_ids);

    // Tạo đơn hàng mới
    $order_data = [
        'user_id'    => $user_id,
        'cart_ids'   => $cart_ids_json,
        'note'       => 'Giao hàng nhanh',
        'ho_ten'     => sanitize_text_field($ho_ten),
        'address'    => sanitize_text_field($address),
        'email'      => sanitize_email($email),
        'phone'      => sanitize_text_field($phone),
        'chiet_khau_dich_vu' => $service_fee, // Lưu phí dịch vụ
        'created_at' => current_time('mysql')
    ];

    $wpdb->insert($table_orders, $order_data);
    $order_id = $wpdb->insert_id;

    if (!$order_id) {
        wp_send_json_error(['message' => 'Lỗi khi tạo đơn hàng!']);
        return;
    }

    wp_send_json_success([
        'message'     => 'Đơn hàng đã được tạo thành công!',
        'order_id'    => $order_id,
        'cart_ids'    => $cart_ids,
        'total_price' => $total_price,
        'service_fee' => $service_fee,
        'updated_products' => $products // Gửi lại danh sách sản phẩm đã cập nhật quantity
    ]);
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
