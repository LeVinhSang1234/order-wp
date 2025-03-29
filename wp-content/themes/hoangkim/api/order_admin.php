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
  $allowed_cart_fields = ['quantity'];

  // Define allowed fields for the orders table
  $allowed_fields = [
    'user_id', 'cart_ids', 'status', 'ho_ten', 'email', 'phone', 'address', 
    'van_don', 'thuong_hieu', 'so_kien_hang', 'da_thanh_toan', 'da_hoan', 
    'exchange_rate', 'phi_mua_hang', 'phi_ship_noi_dia', 'phi_kiem_dem', 
    'phi_gia_co', 'chiet_khau_dich_vu', 'ngay_dat_coc', 'da_mua_hang', 
    'ngay_nhap_kho_tq', 'ngay_nhap_kho_vn', 'ngay_nhan_hang', "kg_tinh_phi",
    'is_gia_co', 'is_kiem_dem_hang', 'is_bao_hiem', 'da_coc',
    "tien_van_chuyen"
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
      $updated = $wpdb->update(
        "{$wpdb->prefix}cart",
        [$field => $value],
        ['id' => $cart_id],
        ['%d'],
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