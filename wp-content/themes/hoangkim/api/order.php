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
                "SELECT id FROM $table_name WHERE user_id = %d AND shop_id = %s AND product_name = %s AND product_image = %s",
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
                    'price'         => floatval($item['price']),
                    'added_at'      => current_time('mysql')
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s']
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
