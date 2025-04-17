<?php

function update_order_admin()
{
  global $wpdb;

  // Validate if `updates` data is sent
  if (empty($_POST['updates'])) {
    wp_send_json_error(["error" => "Dữ liệu không hợp lệ"]);
    exit;
  }

  // Decode JSON from the request
  $updates = json_decode(stripslashes($_POST['updates']), true);
  if (!is_array($updates)) {
    wp_send_json_error(["error" => "Dữ liệu cập nhật không hợp lệ"]);
    exit;
  }

  // Define allowed fields for the cart table
  $allowed_cart_fields = ['quantity', 'price'];

  // Define allowed fields for the orders table
  $allowed_fields = [
    'user_id',
    'cart_ids',
    'status',
    'ho_ten',
    'email',
    'phone',
    'address',
    'van_don',
    'thuong_hieu',
    'so_kien_hang',
    'da_thanh_toan',
    'da_hoan',
    'exchange_rate',
    'phi_mua_hang',
    'phi_ship_noi_dia',
    'phi_kiem_dem',
    'phi_gia_co',
    'chiet_khau_dich_vu',
    'ngay_dat_coc',
    'da_mua_hang',
    'ngay_nhap_kho_tq',
    "ngay_ncc_phat_hang",
    'ngay_nhap_kho_vn',
    'ngay_nhan_hang',
    'is_gia_co',
    'is_kiem_dem_hang',
    'is_bao_hiem',
    'da_coc',
    'percent_coc_truoc', // Add the new field
    'created_at', // Allow updating the created_at field
  ];

  $success_count = 0;
  $errors = [];

  foreach ($updates as $update) {
    $order_id = intval($update['order_id']);
    $field = sanitize_text_field($update['field']);
    $value = isset($update['value']) ? sanitize_text_field($update['value']) : null;

    // Ensure checkbox values are either 0 or 1
    if (in_array($field, ['is_gia_co', 'is_kiem_dem_hang', 'is_bao_hiem', 'da_coc'])) {
      $value = $value == 1 ? 1 : 0;
    }

    // Check if the update is for the cart table
    if (in_array($field, $allowed_cart_fields)) {
      $cart_id = intval($update['cart_id']); // Ensure `cart_id` is provided in the update

      // Use '%f' for price to avoid rounding
      $format = $field === 'price' ? ['%f'] : ['%d'];

      $updated = $wpdb->update(
        "{$wpdb->prefix}cart",
        [$field => $value],
        ['id' => $cart_id],
        $format,
        ['%d']
      );

      if ($updated !== false) {
        $success_count++;
      } else {
        $errors[] = "Không thể cập nhật '$field' cho giỏ hàng ID $cart_id.";
      }
      continue;
    }

    // Check if the field is allowed for the orders table
    if (!in_array($field, $allowed_fields)) {
      $errors[] = "Trường '$field' không được phép cập nhật.";
      continue;
    }

    // Skip updating specific fields if they already have a value
    if (in_array($field, ['ngay_dat_coc', 'da_mua_hang', 'ngay_nhap_kho_tq', 'ngay_nhap_kho_vn', 'ngay_nhan_hang', "ngay_ncc_phat_hang"])) {
      $existing_value = $wpdb->get_var($wpdb->prepare(
        "SELECT $field FROM {$wpdb->prefix}orders WHERE id = %d",
        $order_id
      ));
      if (!empty($existing_value)) {
        $errors[] = "Trường '$field' đã có giá trị và không thể cập nhật.";
        continue;
      }
    }

    // Handle status update and wallet deduction/refund
    if ($field === 'status') {
      if (intval($value) === 6) { // Status "Nhập kho VN"
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}orders WHERE id = %d", $order_id));
        if ($order) {
          $cart_ids_array = json_decode($order->cart_ids, true);
          if (!empty($cart_ids_array)) {
            $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
            $query = $wpdb->prepare(
              "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders)",
              ...$cart_ids_array
            );
            $carts = $wpdb->get_results($query);
          } else {
            $carts = [];
          }

          // Calculate total price
          $totalPrice = array_reduce($carts, function ($carry, $cart) {
            return $carry + ($cart->price * $cart->quantity);
          }, 0);

          $exchange_rate = isset($order->exchange_rate) ? $order->exchange_rate : null;
          if (!$exchange_rate) {
            $exchange_rate = floatval(get_option('exchange_rate', 1.0));
          }
          $total = $totalPrice * $exchange_rate;
          $total += $order->phi_ship_noi_dia * $exchange_rate;
          $total += $order->phi_kiem_dem;
          $total += $order->phi_gia_co * $exchange_rate;
          $total += $order->chiet_khau_dich_vu * $exchange_rate;

          $remaining_amount = $total - intval($order->da_thanh_toan);

          // Get user's current wallet balance
          $current_wallet_balance = floatval(trim(get_user_meta($order->user_id, 'user_wallet', true)));

          // Deduct remaining amount from wallet
          $updated_wallet_balance = $current_wallet_balance - $remaining_amount;

          // Ensure the wallet balance is not negative
          if (floatval($order->da_thanh_toan) < floatval($total)) {
            // Update user's wallet balance
            update_user_meta($order->user_id, 'user_wallet', $updated_wallet_balance);

            // Update the order's `da_thanh_toan` field
            $wpdb->update(
              "{$wpdb->prefix}orders",
              ['da_thanh_toan' => $total],
              ['id' => $order_id],
              ['%f'],
              ['%d']
            );

            // Insert a record into the wallet_transaction table
            $wpdb->insert(
              "{$wpdb->prefix}history_orders_transaction",
              [
                'order_id' => $order_id,
                'loai' => 'Đặt cọc',
                'hinh_thuc' => 'Giao dịch tự xác minh thành công khi hàng về kho',
                'so_tien' => $remaining_amount,
                "user_id" => $order->user_id,
              ],
              ['%d', '%s', '%s', '%f', '%d']
            );
          }
        }
      } elseif (intval($value) === 8) { // Status "Hoàn tiền"
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}orders WHERE id = %d", $order_id));
        if ($order) {
          $refund_amount = floatval($order->da_thanh_toan);

          // Get user's current wallet balance
          $current_wallet_balance = floatval(trim(get_user_meta($order->user_id, 'user_wallet', true)));

          // Add refund amount back to wallet
          $updated_wallet_balance = $current_wallet_balance + $refund_amount;

          // Update user's wallet balance
          update_user_meta($order->user_id, 'user_wallet', $updated_wallet_balance);

          // Update the order's `da_thanh_toan` field to 0
          $wpdb->update(
            "{$wpdb->prefix}orders",
            ['da_thanh_toan' => 0],
            ['id' => $order_id],
            ['%f'],
            ['%d']
          );

          // Insert a record into the wallet_transaction table
          $wpdb->insert(
            "{$wpdb->prefix}history_orders_transaction",
            [
              'order_id' => $order_id,
              'loai' => 'Hoàn tiền',
              'hinh_thuc' => 'Hoàn tiền khi trạng thái đơn hàng là Hủy',
              'so_tien' => $refund_amount,
              "user_id" => $order->user_id,
            ],
            ['%d', '%s', '%s', '%f', '%d']
          );
        }
      }
    }

    // Update the orders table
    $updated = $wpdb->update(
      "{$wpdb->prefix}orders",
      [$field => $value],
      ['id' => $order_id],
      ['%s'],
      ['%d']
    );

    if ($updated !== false) {
      $success_count++;
    } else {
      $errors[] = "Không thể cập nhật '$field' cho đơn hàng ID $order_id.";
    }
  }

  // Return JSON response
  if ($success_count > 0) {
    wp_send_json_success([
      "message" => "$success_count bản ghi đã được cập nhật thành công.",
      "errors" => $errors
    ]);
  } else {
    wp_send_json_error([
      "message" => "Không có bản ghi nào được cập nhật.",
      "errors" => $errors
    ]);
  }
  exit;
}

add_action('wp_ajax_update_order_admin', 'update_order_admin');

function delete_package()
{
  global $wpdb;

  $package_id = intval($_POST['package_id']);

  $deleted = $wpdb->delete(
    "{$wpdb->prefix}packages",
    ['id' => $package_id],
    ['%d']
  );

  if ($deleted !== false) {
    wp_send_json_success(["message" => "Xóa thành công."]);
  } else {
    wp_send_json_error(["message" => "Xóa thất bại."]);
  }
  exit;
}
add_action('wp_ajax_delete_package', 'delete_package');

function update_packages()
{
  global $wpdb;

  if (empty($_POST['packages'])) {
    wp_send_json_error(["message" => "Dữ liệu không hợp lệ."]);
    exit;
  }

  $packages = json_decode(stripslashes($_POST['packages']), true);
  if (!is_array($packages)) {
    wp_send_json_error(["message" => "Dữ liệu kiện hàng không hợp lệ."]);
    exit;
  }

  $allowed_fields = ['ma_kien', 'can_nang', 'the_tich', 'trang_thai_kien', 'order_id', 'rate_rieng', 'tien_van_chuyen'];
  $success_count = 0;
  $errors = [];

  foreach ($packages as $package) {
    $package_id = isset($package['package_id']) ? intval($package['package_id']) : null;
    $order_id = intval($package['order_id']); // Ensure order_id is always used
    $data = ['order_id' => $order_id]; // Include order_id in the data array

    foreach ($package as $field => $value) {
      if (in_array($field, $allowed_fields)) {
        $data[$field] = sanitize_text_field($value);
      }
    }

    if ($package_id) {
      $updated = $wpdb->update(
        "{$wpdb->prefix}packages",
        $data,
        ['id' => $package_id],
        array_fill(0, count($data), '%s'),
        ['%d']
      );
      if ($updated !== false) {
        $success_count++;
      } else {
        $errors[] = "Không thể cập nhật kiện hàng ID $package_id.";
      }
    } else {
      $inserted = $wpdb->insert(
        "{$wpdb->prefix}packages",
        $data,
        array_fill(0, count($data), '%s')
      );
      if ($inserted !== false) {
        $success_count++;
      } else {
        $errors[] = "Không thể thêm kiện hàng mới.";
      }
    }

    // Calculate total weight and update `tien_van_chuyen` in the orders table
    $total_weight = $wpdb->get_var($wpdb->prepare(
      "SELECT SUM(can_nang) FROM {$wpdb->prefix}packages WHERE order_id = %d",
      $order_id
    ));
    if ($total_weight < 0.5) {
      $total_weight = 0.5;
    }

    $price_per_kg = get_option('price_per_kg', 0.0);

    if ($package_id) {
      $rate_rieng = $wpdb->get_var($wpdb->prepare(
        "SELECT rate_rieng FROM {$wpdb->prefix}packages WHERE id = %d",
        $package_id
      ));
      $rate = $rate_rieng !== null ? floatval($rate_rieng) : floatval($price_per_kg);
    } else {
      $rate = floatval($price_per_kg);
    }
    $shipping_cost = $total_weight *  $rate;

    $wpdb->update(
      "{$wpdb->prefix}orders",
      ['tien_van_chuyen' => $shipping_cost],
      ['id' => $order_id],
      ['%f'],
      ['%d']
    );
  }

  if ($success_count > 0) {
    wp_send_json_success(["message" => "$success_count kiện hàng đã được cập nhật thành công.", "errors" => $errors]);
  } else {
    wp_send_json_error(["message" => "Không có kiện hàng nào được cập nhật.", "errors" => $errors]);
  }
  exit;
}
add_action('wp_ajax_update_packages', 'update_packages');
