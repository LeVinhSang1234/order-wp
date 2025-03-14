<?php
add_action('wp_ajax_save_cart', 'save_cart');

function save_cart()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(["message" => "❌ Bạn chưa đăng nhập!"], 401);
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    $json = file_get_contents('php://input');
    $user_id = get_current_user_id();
    $cart_items = json_decode($json, true);
    if (empty($cart_items)) {
        wp_send_json_error(["message" => "❌ Giỏ hàng trống!"], 400);
        wp_die();
    }

    foreach ($cart_items as $item) {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE user_id = %d AND shop_id = %s AND product_name = %s AND product_image = %s AND is_done = 0",
                $user_id,
                sanitize_text_field($item['id']),
                sanitize_text_field($item['name']),
                sanitize_text_field($item['image']),
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
                    'added_at'      => current_time('mysql')
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s']
            );
            if ($result === false) {
                wp_send_json_error([
                    "message"   => "❌ Lưu giỏ hàng thất bại!",
                    "mysql_error" => $wpdb->last_error
                ], 500);
                wp_die();
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
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cart';
    $user_id = get_current_user_id();

    $json = file_get_contents('php://input');
    $cart_item = json_decode($json, true);
    if (empty($cart_item) || !isset($cart_item['id'])) {
        wp_send_json_error(["message" => "❌ Không có id sản phẩm để xóa!"], 400);
        wp_die();
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
        wp_die();
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
        wp_die();
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
        wp_die();
    }
    wp_send_json_success(['message' => '✅ Giỏ hàng đã được cập nhật thành công!']);
}
add_action('wp_ajax_update_cart_item', 'update_cart_item_via_ajax');

function update_cart_quantity()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        wp_die();
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
        wp_die();
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
        wp_die();
    }
    wp_send_json_success(['message' => '✅ Số lượng đã được cập nhật thành công!']);
}
add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');


function cancel_order()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        wp_die();
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
        wp_die();
    }
    $result = $wpdb->update(
        $order_table,
        [
            'status' => 12,
        ],
        ['id' => $order_id, 'user_id' => $user_id],
        ['%d', '%d'],
    );
    if ($result === false) {
        wp_send_json_error(['message' => '❌ Huỷ đơn hàng không thành công!'], 500);
        wp_die();
    }
    wp_send_json_success(['message' => '✅ Huỷ đơn hàng thành công!']);
}
add_action('wp_ajax_cancel_order', 'cancel_order');



add_action('wp_ajax_send_chat', 'send_chat');

function send_chat()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => '❌ Bạn chưa đăng nhập!'], 401);
        wp_die();
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';

    global $wpdb;
    $table_name = $wpdb->prefix . 'chat';
    $user_id = get_current_user_id();
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
