<?php

function register_order_menu()
{
  add_menu_page("Quản lý đơn hàng", "Đơn hàng", "manage_options", "order_list", "render_order_page");
  add_submenu_page(null, "Chi tiết đơn hàng", "Chi tiết đơn hàng", "manage_options", "order_detail", "render_order_detail");
}
add_action("admin_menu", "register_order_menu");


function update_order_ajax()
{
  global $wpdb;

  // Kiểm tra nếu dữ liệu `updates` được gửi lên
  if (!isset($_POST['updates'])) {
    wp_send_json_error(["error" => "Dữ liệu không hợp lệ"]);
    exit;
  }

  // Giải mã JSON từ request
  $updates = json_decode(stripslashes($_POST['updates']), true);
  if (!is_array($updates)) {
    wp_send_json_error(["error" => "Dữ liệu cập nhật không hợp lệ"]);
    exit;
  }

  // Danh sách các trường hợp lệ để cập nhật
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
    'chiet_khau_dich_vu'
  ];

  // Lưu lại số bản ghi cập nhật thành công
  $success_count = 0;
  $errors = [];

  foreach ($updates as $update) {
    $order_id = intval($update['order_id']);
    $field = sanitize_text_field($update['field']);
    $value = isset($update['value']) ? sanitize_text_field($update['value']) : null;

    // Kiểm tra nếu trường hợp lệ
    if (!in_array($field, $allowed_fields)) {
      $errors[] = "Field '$field' không hợp lệ.";
      continue;
    }

    // Cập nhật cơ sở dữ liệu
    $updated = $wpdb->update(
      "{$wpdb->prefix}orders",
      [$field => $value],
      ['id' => $order_id],
      ['%s'],
      ['%d']
    );

    // Kiểm tra kết quả cập nhật
    if ($updated !== false) {
      $success_count++;
    } else {
      $errors[] = "Không thể cập nhật '$field' cho đơn hàng ID $order_id.";
    }
  }

  // Trả kết quả JSON
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

add_action('wp_ajax_update_order', 'update_order_ajax');
function render_order_detail()
{
  global $wpdb;

  if (!isset($_GET['id'])) {
    echo '<div class="error"><p>Không tìm thấy đơn hàng!</p></div>';
    return;
  }

  $order_id = intval($_GET['id']);
  $order = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}orders WHERE id = $order_id");

  if (!$order) {
    echo '<div class="error"><p>Đơn hàng không tồn tại!</p></div>';
    return;
  }

  $field_labels = [
    'id' => 'Mã đơn hàng',
    'user_id' => 'ID Người dùng',
    'status' => 'Trạng thái',
    'created_at' => 'Ngày tạo',
    'total_price' => 'Tổng tiền',
    'payment_method' => 'Phương thức thanh toán',
    'updated_at' => 'Ngày cập nhật',
    'note' => 'Ghi chú',
    'is_gia_co' => 'Gia cố',
    'is_kiem_dem_hang' => 'Kiểm đếm hàng',
    'is_bao_hiem' => 'Bảo hiểm',
    'ho_ten' => 'Họ tên',
    'phone' => 'Sđt',
    'da_thanh_toan' => 'Đã thanh toán',
    'da_hoan' => 'Đã hoàn',
    'phi_kiem_dem' => 'Phí kiểm đếm',
    'so_kien_hang' => 'Số kiện hàng',
    'phi_mua_hang' => 'Phí mua hàng',
    'address' => 'Địa chỉ',
    'van_don' => 'Vận đơn',
    'exchange_rate' => 'Tỷ giá',
    'phi_gia_co' => 'Phí gia cố',
    'phi_ship_noi_dia' => 'Phí ship nội địa',
    'chiet_khau_dich_vu' => 'Chiết khấu dịch vụ',
    "brand" => "Tên hàng hóa"
  ];

  $editable_fields = [
    'da_thanh_toan',
    'da_hoan',
    'phi_kiem_dem',
    'so_kien_hang',
    'phi_mua_hang',
    "phi_ship_noi_dia",
    "phi_gia_co",
    "exchange_rate",
    "chiet_khau_dich_vu"
  ];

  echo "<div class='wrap'><h2>Chi tiết đơn hàng #{$order->id}</h2>";

  echo "<div class='order-card'>";

  foreach ($order as $field => $value) {
    $label = isset($field_labels[$field]) ? $field_labels[$field] : ucfirst(str_replace('_', ' ', $field));
    $editable = in_array($field, $editable_fields) ? "contenteditable='true'" : "";
    $class = in_array($field, $editable_fields) ? "class='editable'" : "";
    echo "<div class='order-item'>
            <strong>{$label}:</strong>
            <div {$editable} $class data-id='{$order->id}' data-field='{$field}'>{$value}</div>
          </div>";
  }

  echo "</div>";
  echo "<button id='updateOrder' class='button-primary'>Cập nhật</button>";
  echo "<a href='" . admin_url("admin.php?page=order_list") . "' class='button'>Quay lại danh sách</a>";
  echo "</div>";

  ?>
  <style>
    .order-card {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      width: 100%;
      display: flex;
      overflow: auto;
      flex-wrap: wrap;
      gap: 16px;
    }

    .order-item {
      margin-bottom: 10px;
      border-bottom: 1px solid #eee;
      padding-bottom: 8px;
      width: 200px;
    }

    .order-item:last-child {
      border-bottom: none;
    }

    .editable {
      display: block;
      padding: 4px;
      background: #f9f9f9;
      border-radius: 4px;
      cursor: pointer;
      height: 20px;
      border: 1px solid
    }

    .editable:focus {
      background: #e6f7ff;
      outline: none;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <script>
    jQuery(document).ready(function ($) {
      $("#updateOrder").click(function () {
        let updates = [];

        $(".editable[contenteditable='true']").each(function () {
          updates.push({
            order_id: $(this).data("id"),
            field: $(this).data("field"),
            value: $(this).text().trim() || null
          });
        });

        console.log("Dữ liệu gửi đi:", updates);

        $.ajax({
          url: '<?php echo admin_url("admin-ajax.php"); ?>',
          type: "POST",
          data: {
            action: "update_order_fields",
            updates: JSON.stringify(updates)
          },
          beforeSend: function () {
            console.log("Đang gửi yêu cầu...");
          },
          success: function (response) {
            console.log("Phản hồi từ server:", response);
            if (response.success) {
              alert("Cập nhật thành công!");
            } else {
              alert("Cập nhật thất bại! Lỗi: " + response.data.message);
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.error("Lỗi AJAX:", textStatus, errorThrown);
            alert("Có lỗi xảy ra khi gửi yêu cầu.");
          }
        });
      });
    });
  </script>
  <?php
}
