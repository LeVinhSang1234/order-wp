<?php
add_action('wp_ajax_save_cart', 'save_cart');

function save_cart()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(["message" => "❌ Bạn chưa đăng nhập!"], 401);
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    $json = file_get_contents('php://input');
    $user_id = get_current_user_id();
    $cart_items = json_decode($json, true);
    if (empty($cart_items)) {
        wp_send_json_error(["message" => "❌ Giỏ hàng trống!"], 400);
        exit;
    }

    foreach ($cart_items as $item) {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE user_id = %d AND shop_id = %s AND product_name = %s AND product_image = %s AND color = %s AND size = %s AND is_done = 0",
                $user_id,
                sanitize_text_field($item['id']),
                sanitize_text_field($item['name']),
                sanitize_text_field($item['image']),
                sanitize_text_field($item['color']),
                sanitize_text_field($item['size']),
            )
        );
        if ($exists) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $table_name SET quantity = quantity + %d WHERE id = %d",
                    intval($item['quantity']),
                    $exists
                )
            );
        } else {
            $result = $wpdb->insert(
                $table_name,
                [
                    'user_id'       => $user_id,
                    'shop_url'      => sanitize_text_field($item['shopUrl']),
                    'shop_id'       => sanitize_text_field($item['id']),
                    'product_name'  => sanitize_text_field($item['name']),
                    'product_image' => esc_url_raw($item['image']),
                    'product_note' => sanitize_text_field($item['note']),
                    'product_url'   => esc_url_raw($item['url']),
                    'web'           => sanitize_text_field($item['web']),
                    'quantity'      => intval($item['quantity']),
                    'price'         => floatval(str_replace('?', '0', $item['price'])),
                    'size'      => sanitize_text_field($item['size']),
                    'color'      => sanitize_text_field($item['color']),
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s', '%s', '%s']
            );
            if ($result === false) {
                wp_send_json_error([
                    "message"   => "❌ Lưu giỏ hàng thất bại!",
                    "mysql_error" => $wpdb->last_error
                ], 500);
                exit;
            }
        }
    }
    wp_send_json_success([
        "message"   => "✅ Giỏ hàng đã lưu thành công!",
        "status"    => "success"
    ]);
}

function remove_cart()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(["message" => "❌ Bạn chưa đăng nhập!"], 401);
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    $json = file_get_contents('php://input');
    $cart_item = json_decode($json, true);
    if (empty($cart_item) || !isset($cart_item['id'])) {
        wp_send_json_error(["message" => "❌ Không có id sản phẩm để xóa!"], 400);
        exit;
    }
    $product_id = intval($cart_item['id']);
    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d AND id = %d",
            $user_id,
            $product_id
        )
    );

    if ($exists) {
        $wpdb->delete(
            $table_name,
            ['id' => $product_id],
            ['%d']
        );

        wp_send_json_success([
            "message" => "✅ Sản phẩm đã được xóa khỏi giỏ hàng!",
            "status"  => "success"
        ]);
    } else {
        wp_send_json_error([
            "message" => "❌ Sản phẩm không tồn tại trong giỏ hàng!"
        ], 404);
    }
}

add_action('wp_ajax_remove_cart', 'remove_cart');

function update_cart_item_via_ajax()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    global $wpdb;
    $cart_table = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    $json = file_get_contents('php://input');
    $body = json_decode($json, true);

    if (isset($body['cart_id'], $body['quantity'], $body['is_select'])) {
        $cart_id = intval($body['cart_id']);
        $quantity = intval($body['quantity']);
        $is_select = intval($body['is_select']);
    }
    $cart_item = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $cart_table WHERE id = %d AND user_id = %d",
        $cart_id,
        $user_id
    ));

    if (!$cart_item) {
        wp_send_json_error(['message' => '❌ Không tìm thấy sản phẩm trong giỏ hàng của bạn!'], 404);
        exit;
    }
    $result = $wpdb->update(
        $cart_table,
        [
            'quantity' => $quantity,
            'is_select' => $is_select
        ],
        ['id' => $cart_id, 'user_id' => $user_id],
        ['%d', '%d'],
        ['%d', '%d']
    );
    if ($result === false) {
        wp_send_json_error(['message' => '❌ Cập nhật giỏ hàng không thành công!'], 500);
        exit;
    }
    wp_send_json_success(['message' => '✅ Giỏ hàng đã được cập nhật thành công!']);
}
add_action('wp_ajax_update_cart_item', 'update_cart_item_via_ajax');

function update_cart_quantity()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    global $wpdb;
    $cart_table = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    if (isset($_POST['cart_id'], $_POST['quantity'])) {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
    }

    $cart_item = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $cart_table WHERE id = %d AND user_id = %d",
        $cart_id,
        $user_id
    ));

    if (!$cart_item) {
        wp_send_json_error(['message' => '❌ Không tìm thấy sản phẩm trong giỏ hàng của bạn!'], 404);
        exit;
    }

    $result = $wpdb->update(
        $cart_table,
        [
            'quantity' => $quantity,
        ],
        ['id' => $cart_id, 'user_id' => $user_id],
        ['%d', '%d'],
    );

    if ($result === false) {
        wp_send_json_error(['message' => '❌ Cập nhật giỏ hàng không thành công!'], 500);
        exit;
    }
    if ($result === false) {
        wp_send_json_error(['message' => '❌ Cập nhật giỏ hàng không thành công!'], 500);
        exit;
    }
    wp_send_json_success(['message' => '✅ Số lượng đã được cập nhật thành công!']);
}
add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');


function cancel_order()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    global $wpdb;
    $order_table = $wpdb->prefix . 'orders';
    $user_id = get_current_user_id();

    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);
    }
    $order_item = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $order_table WHERE id = %d AND user_id = %d",
        $order_id,
        $user_id
    ));

    if (!$order_item) {
        wp_send_json_error(['message' => '❌ Không tìm thấy sản phẩm trong giỏ hàng của bạn!'], 404);
        exit;
    }
    $result = $wpdb->update(
        $order_table,
        [
            'status' => 8,
        ],
        ['id' => $order_id, 'user_id' => $user_id],
        ['%d', '%d'],
    );
    if ($result === false) {
        wp_send_json_error(['message' => '❌ Huỷ đơn hàng không thành công!'], 500);
        exit;
    }
    insert_notification("Huỷ đơn hàng", "Bạn đã huỷ đơn hàng thành công", array(array("order_id" => $order_id)));
    wp_send_json_success(['message' => '✅ Huỷ đơn hàng thành công!']);
}
add_action('wp_ajax_cancel_order', 'cancel_order');



add_action('wp_ajax_send_chat', 'send_chat');

function send_chat()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
    $user_id = get_current_user_id();

    global $wpdb;
    $table_name = $wpdb->prefix . 'chat';

    $result = $wpdb->insert(
        $table_name,
        [
            'user_id'   => $user_id,
            'text'      => $text,
            'order_id'  => $order_id,
        ],
        ['%d', '%s', '%d']
    );
    if ($result !== false) {
        wp_send_json_success(array('message' => 'Chat thành công'));
    } else {
        wp_send_json_error(array('message' => 'Chat thất bại, vui lòng thử lại.'));
    }
    exit;
}

add_action('wp_ajax_send_khieu_nai', 'send_khieu_nai');

function send_khieu_nai()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
    $user_id = get_current_user_id();

    global $wpdb;
    $table_name = $wpdb->prefix . 'chat';
    $order_table = $wpdb->prefix . 'orders';

    $order_item = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $order_table WHERE id = %d AND user_id = %d",
        $order_id,
        $user_id
    ));

    if (!$order_item) {
        wp_send_json_error(['message' => '❌ Không tìm thấy sản phẩm trong giỏ hàng của bạn!'], 404);
        exit;
    }

    $result = $wpdb->insert(
        $table_name,
        [
            'user_id'   => $user_id,
            'text'      => "<span style='font-size: 12px'>Bạn đã khiếu nại với lý do</span>:\n" . '<span style="color: #ff0000">' . $text . '</span>',
            'order_id'  => $order_id,
            'is_system' => 1,
        ],
        ['%d', '%s', '%d', '%d']
    );
    if ($result === false) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra, vui lòng thử lại.'));
        exit;
    }

    $result = $wpdb->update(
        $order_table,
        [
            'status' => 9,
        ],
        ['id' => $order_id, 'user_id' => $user_id],
        ['%d', '%d'],
    );
    if ($result !== false) {
        insert_notification("Khiếu nại đơn hàng", "Bạn đã khiếu nại đơn hàng thành công với lý do: " . $text, array(array("order_id" => $order_id)));
        wp_send_json_success(array('message' => 'Khiếu nại thành công'));
    } else {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra, vui lòng thử lại.'));
    }
    exit;
}

add_action('wp_ajax_create_order', 'create_order_via_ajax');
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
        "SELECT id FROM {$wpdb->prefix}cart WHERE shop_id = %d AND user_id = %d AND is_done = 0",
        $shop_id,
        $user_id
    ));
    if (empty($cart_ids)) {
        wp_send_json_error(['message' => 'Không tìm thấy giỏ hàng cho cửa hàng này.']);
        exit;
    }

    // Tính tổng giá trị đơn hàng
    $total_price = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(price * quantity) FROM {$wpdb->prefix}cart WHERE id IN (" . implode(',', array_fill(0, count($cart_ids), '%d')) . ")",
        ...$cart_ids
    ));

    // Tính phí dịch vụ dựa trên tổng giá trị đơn hàng
    if ($total_price < 5000000) {
        $service_fee = $total_price * 0.03; // 3%
    } elseif ($total_price >= 5000000 && $total_price <= 50000000) {
        $service_fee = $total_price * 0.02; // 2%
    } else {
        $service_fee = $total_price * 0.015; // 1.5%
    }

    $cart_ids_str = json_encode($cart_ids);
    $note = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';
    $ho_ten = isset($_POST['ho_ten']) ? sanitize_textarea_field($_POST['ho_ten']) : '';
    $address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
    $email = isset($_POST['email']) ? sanitize_textarea_field($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_textarea_field($_POST['phone']) : '';
    $is_gia_co = isset($_POST['is_gia_co']) ? intval($_POST['is_gia_co']) : 0;
    $is_kiem_dem_hang = isset($_POST['is_kiem_dem_hang']) ? intval($_POST['is_kiem_dem_hang']) : 0;
    $is_bao_hiem = isset($_POST['is_bao_hiem']) ? intval($_POST['is_bao_hiem']) : 0;

    // Lưu thông tin đơn hàng vào database
    $table = $wpdb->prefix . 'orders';
    $data = [
        'user_id' => $user_id,
        'cart_ids' => $cart_ids_str,
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
        insert_notification("Tạo đơn hàng", "Bạn đã tạo đơn hàng thành công", [["order_id" => $wpdb->insert_id]]);
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

add_action('wp_ajax_update_order_fields', 'update_order_fields');

function update_order_fields()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Bạn cần đăng nhập để tạo đơn hàng.']);
        exit;
    }
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    global $wpdb;
    $order_table = $wpdb->prefix . 'orders';
    $order_item = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $order_table WHERE id = %d AND user_id = %d",
        $order_id,
        $user_id
    ));

    if (!$order_item) {
        wp_send_json_error(['message' => '❌ Không tìm thấy sản phẩm trong giỏ hàng của bạn!'], 404);
        exit;
    }

    $note = isset($_POST['note']) ? sanitize_text_field($_POST['note']) : '';
    $is_gia_co = isset($_POST['is_gia_co']) ? intval($_POST['is_gia_co']) : 0;
    $is_kiem_dem_hang = isset($_POST['is_kiem_dem_hang']) ? intval($_POST['is_kiem_dem_hang']) : 0;
    $is_bao_hiem = isset($_POST['is_bao_hiem']) ? intval($_POST['is_bao_hiem']) : 0;
    if ($order_id <= 0) {
        wp_send_json_error(array('message' => 'ID đơn hàng không hợp lệ.'));
    }
    $table_name = $wpdb->prefix . 'orders';

    $data = array(
        'note' => $note,
        'is_gia_co' => $is_gia_co,
        'is_kiem_dem_hang' => $is_kiem_dem_hang,
        'is_bao_hiem' => $is_bao_hiem
    );

    $where = array('id' => $order_id);
    $updated = $wpdb->update($table_name, $data, $where);
    if ($updated !== false) {
        wp_send_json_success(array('message' => 'Đơn hàng đã được cập nhật thành công.'));
    } else {
        wp_send_json_error(array('message' => 'Cập nhật thất bại, vui lòng thử lại.'));
    }
    exit;
}

add_action('wp_ajax_read_notification', 'read_notification');

function read_notification()
{
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $note = isset($_POST['note']) ? sanitize_text_field($_POST['note']) : '';
    $is_gia_co = isset($_POST['is_gia_co']) ? intval($_POST['is_gia_co']) : 0;
    $is_kiem_dem_hang = isset($_POST['is_kiem_dem_hang']) ? intval($_POST['is_kiem_dem_hang']) : 0;
    $is_bao_hiem = isset($_POST['is_bao_hiem']) ? intval($_POST['is_bao_hiem']) : 0;
    if ($order_id <= 0) {
        wp_send_json_error(array('message' => 'ID đơn hàng không hợp lệ.'));
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'orders';

    $data = array(
        'note' => $note,
        'is_gia_co' => $is_gia_co,
        'is_kiem_dem_hang' => $is_kiem_dem_hang,
        'is_bao_hiem' => $is_bao_hiem
    );

    $where = array('id' => $order_id);
    $updated = $wpdb->update($table_name, $data, $where);
    if ($updated !== false) {
        wp_send_json_success(array('message' => 'Đơn hàng đã được cập nhật thành công.'));
    } else {
        wp_send_json_error(array('message' => 'Cập nhật thất bại, vui lòng thử lại.'));
    }
    exit;
}


add_action('wp_ajax_create_order_ki_gui', 'create_order_ki_gui');
function create_order_ki_gui()
{
    // Kiểm tra nonce bảo mật
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_order_nonce')) {
        wp_send_json_error(['message' => 'Nonce không hợp lệ']);
        exit;
    }
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Bạn cần đăng nhập để tạo đơn hàng.']);
        exit;
    }
    global $wpdb;
    $current_user = wp_get_current_user();
    $cart_ids_str = json_encode(array());
    $note = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';
    $brand = isset($_POST['brand']) ? sanitize_textarea_field($_POST['brand']) : '';
    $thuong_hieu = isset($_POST['thuong_hieu']) ? sanitize_textarea_field($_POST['thuong_hieu']) : '';
    $van_don = isset($_POST['van_don']) ? sanitize_textarea_field($_POST['van_don']) : '';
    $so_kien_hang = isset($_POST['so_kien_hang']) ? intval($_POST['so_kien_hang']) : '';

    if (!$van_don) {
        wp_send_json_error(['message' => 'Vận đơn là bắt buộc']);
        exit;
    }

    $ho_ten = $current_user->display_name;
    $address = display_user_address();
    $email = $current_user->user_email;
    $phone = display_user_phone();
    $table = $wpdb->prefix . 'orders';
    $data = [
        'user_id' => $user_id,
        'cart_ids' => $cart_ids_str,
        'brand' => $brand,
        'note' => $note,
        'thuong_hieu' => $thuong_hieu,
        'ho_ten' => $ho_ten,
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
        'van_don' => $van_don,
        'type' => 1,
        'so_kien_hang' => $so_kien_hang,
        'is_gia_co' => 4, // Đơn hàng ký gửi
        "status" => 4, // Trạng thái đơn hàng ký gửi
    ];
    $format = [
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
    ];
    $result = $wpdb->insert($table, $data, $format);
    if ($result !== false) {
        insert_notification("Tạo đơn hàng ký gửi", "Bạn đã tạo đơn hàng ký gửi thành công", array(array("order_id" => $wpdb->insert_id)));
        wp_send_json_success(['message' => 'Đơn hàng đã được tạo thành công.']);
    } else {
        wp_send_json_error(['message' => 'Lỗi khi tạo đơn hàng.']);
    }
    exit;
}

add_action('wp_ajax_update_order_status', 'update_order_status');

function update_order_status()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        exit;
    }

    global $wpdb;
    $order_table = $wpdb->prefix . 'orders';
    $history_table = $wpdb->prefix . 'history_orders_transaction';
    $user_id = get_current_user_id();

    $order_ids = isset($_POST['order_ids']) ? array_map('intval', $_POST['order_ids']) : [];
    $deposits = isset($_POST['deposits']) ? array_map('floatval', $_POST['deposits']) : [];
    $total_deposit = isset($_POST['total_deposit']) ? floatval($_POST['total_deposit']) : 0;

    if (empty($order_ids) || count($order_ids) !== count($deposits)) {
        wp_send_json_error(['message' => '❌ Dữ liệu không hợp lệ!'], 400);
        exit;
    }

    // Deduct total deposit from user's wallet
    $user_wallet = floatval(get_user_meta($user_id, 'user_wallet', true));
    if ($user_wallet < $total_deposit) {
        wp_send_json_error(['message' => '❌ Số dư ví không đủ để đặt cọc!'], 400);
        exit;
    }
    $new_wallet_balance = $user_wallet - $total_deposit;
    update_user_meta($user_id, 'user_wallet', $new_wallet_balance);

    foreach ($order_ids as $index => $order_id) {
        $deposit = $deposits[$index];

        $result = $wpdb->update(
            $order_table,
            [
                'status' => 2,
                'da_thanh_toan' => $deposit
            ],
            [
                'id' => $order_id,
                'user_id' => $user_id
            ],
            ['%d', '%f'],
            ['%d', '%d']
        );

        if ($result === false) {
            wp_send_json_error(['message' => '❌ Cập nhật trạng thái đơn hàng không thành công!'], 500);
            exit;
        }

        // Insert record into history_orders_transaction
        $wpdb->insert(
            $history_table,
            [
                'order_id' => $order_id,
                'loai' => 'Đặt cọc',
                'hinh_thuc' => 'Thanh toán trước 80% đơn hàng',
                'so_tien' => $deposit,
                'user_id' => $user_id,
                'nguoi_thuc_hien' => $user_id, // Add current user as the executor
            ],
            ['%d', '%s', '%s', '%f', '%d', '%d']
        );
    }

    wp_send_json_success(['message' => '✅ Trạng thái và tiền thanh toán đã được cập nhật thành công! Số dư ví còn lại: ' . number_format($new_wallet_balance) . ' VNĐ']);
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
    $shop_id = $data['shop_id'] ?? '';
    $products_input = $data['products'] ?? [];

    if (empty($products_input)) {
        wp_send_json_error(['message' => 'Không có sản phẩm nào được chọn!']);
        return;
    }

    $cart_ids = [];
    $total_price = 0;

    foreach ($products_input as $input_product) {
        $product_id = $input_product['id'];
        $quantity = intval($input_product['quantity']);

        $product_data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_cart WHERE id = %d AND user_id = %d AND is_done = 0", $product_id, $user_id),
            ARRAY_A
        );
        if (!$product_data) {
            continue;
        }
        // Cập nhật quantity vào product
        $product_data['quantity'] = $quantity;
        // Cập nhật database
        $wpdb->update(
            $table_cart,
            ['is_done' => 1, 'quantity' => $quantity],
            ['id' => $product_id, 'user_id' => $user_id],
            ['%d', '%d'],
            ['%d', '%d']
        );
        // Tính tổng tiền
        $cart_ids[] = (string) $product_id;
        $total_price += floatval($product_data['price']) * $quantity;
    }
    // Tính phí dịch vụ dựa trên tổng giá
    if ($total_price < 5000000) {
        $service_fee = $total_price * 0.03; // 3%
    } elseif ($total_price >= 5000000 && $total_price <= 50000000) {
        $service_fee = $total_price * 0.02; // 2%
    } else {
        $service_fee = $total_price * 0.015; // 1.5%
    }

    $cart_ids_json = json_encode($cart_ids);

    $order_data = [
        'user_id'    => $user_id,
        'cart_ids'   => $cart_ids_json,
        'note'       => 'Giao hàng nhanh',
        'ho_ten'     => sanitize_text_field($ho_ten),
        'address'    => sanitize_text_field($address),
        'email'      => sanitize_email($email),
        'phone'      => sanitize_text_field($phone),
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
    ]);
}

add_action('wp_ajax_recreate_order', 'handle_recreate_order');
add_action('wp_ajax_nopriv_recreate_order', 'handle_recreate_order');

function handle_recreate_order()
{
    global $wpdb;

    // Kiểm tra quyền truy cập
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Bạn cần đăng nhập để thực hiện thao tác này.']);
    }

    // Lấy ID đơn hàng từ yêu cầu
    $order_id = isset($_POST['order_id']) ? sanitize_text_field($_POST['order_id']) : '';

    if (empty($order_id)) {
        wp_send_json_error(['message' => 'ID đơn hàng không hợp lệ.']);
    }

    $table_name = $wpdb->prefix . 'orders';

    // Lấy dữ liệu đơn hàng cũ
    $old_order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $order_id), ARRAY_A);
    if (!$old_order) {
        wp_send_json_error(['message' => 'Đơn hàng không tồn tại.']);
    }

    // Tạo dữ liệu cho đơn hàng mới
    $new_order = $old_order;
    unset($new_order['id']); // Loại bỏ ID cũ để tạo bản ghi mới
    $new_order['status'] = 1; // Đặt trạng thái mới
    $new_order['da_thanh_toan'] = 0; // Đặt lại số tiền đã thanh toán
    $new_order['da_hoan'] = 0; // Đặt lại số tiền đã hoàn
    $new_order['created_at'] = current_time('mysql'); // Cập nhật ngày tạo mới

    // Thêm đơn hàng mới vào cơ sở dữ liệu
    $inserted = $wpdb->insert($table_name, $new_order);

    if ($inserted === false) {
        wp_send_json_error(['message' => 'Không thể tạo đơn hàng mới.']);
    }

    wp_send_json_success(['message' => 'Đơn hàng mới đã được tạo thành công!']);
}
