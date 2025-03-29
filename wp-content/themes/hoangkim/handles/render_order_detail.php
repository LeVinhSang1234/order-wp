<?php

function register_order_menu()
{
  add_menu_page("Quản lý đơn hàng", "Đơn hàng", "manage_options", "order_list", "render_order_page");
  add_submenu_page(null, "Chi tiết đơn hàng", "Chi tiết đơn hàng", "manage_options", "order_detail", "render_order_detail");
}
add_action("admin_menu", "register_order_menu");

function render_order_detail()
{
  global $wpdb;

  if (!isset($_GET['id'])) {
    echo '<div class="error"><p>Không tìm thấy đơn hàng!</p></div>';
    return;
  }

  $order_id = intval($_GET['id']);
  $order = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}orders WHERE id = $order_id");
  $cart_ids_array = json_decode($order->cart_ids, true);
  $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
  $query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders)",
    ...$cart_ids_array
  );
  $carts = $wpdb->get_results($query);

  if (!$order) {
    echo '<div class="error"><p>Đơn hàng không tồn tại!</p></div>';
    return;
  }

  $field_labels = [
    'id' => 'Mã đơn hàng',
    'user_id' => 'ID Người dùng',
    'status' => 'Trạng thái',
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
    'phi_gia_co' => 'Phí gia cố(¥)',
    'phi_ship_noi_dia' => 'Phí ship nội địa(¥)',
    'chiet_khau_dich_vu' => 'Chiết khấu dịch vụ',
    "brand" => "Tên hàng hóa",
    'created_at' => 'Ngày tạo',
    'tien_van_chuyen' => 'Tiền vận chuyển',
    'kg_tinh_phi' => 'Tổng kg tính phí',
    "da_coc" => "Đã đặt cọc",
  ];

  $editable_fields = [
    'da_thanh_toan',
    'da_hoan',
    'phi_kiem_dem',
    "phi_ship_noi_dia",
    "phi_gia_co",
    "exchange_rate",
    "chiet_khau_dich_vu",
    "tien_van_chuyen",
    "kg_tinh_phi",
    "da_coc",
  ];

  $hidden_fields = [
    'so_kien_hang', 
    'phi_mua_hang',
    "ngay_dat_coc",
    "da_mua_hang",
    "ngay_nhap_kho_tq",        
    "ngay_nhap_kho_tq",
    "ngay_nhap_kho_vn",
    "ngay_nhan_hang",
    "ngay_ncc_phat_hang",
    "user_id",
    "cart_ids",
    "type"
  ];

  $checkbox_fields = ['is_gia_co', 'is_kiem_dem_hang', 'is_bao_hiem', 'da_coc'];

  $exchange_rate = isset($order->exchange_rate) ? $order->exchange_rate : null;
  if (!$exchange_rate) {
    $exchange_rate = floatval(get_option('exchange_rate', 1.0));
  }

  echo "<div class='wrap'><h2>Chi tiết đơn hàng #{$order->id}</h2>";

  echo "<div class='order-card'>";

  foreach ($order as $field => $value) {
    if (in_array($field, $hidden_fields)) {
        continue; // Skip hidden fields
    }
    $label = isset($field_labels[$field]) ? $field_labels[$field] : ucfirst(str_replace('_', ' ', $field));
    $editable = in_array($field, $editable_fields) ? "contenteditable='true'" : "";
    $class = in_array($field, $editable_fields) ? "class='editable'" : "";
    if (in_array($field, $checkbox_fields)) {
      $checked = $value == 1 ? "checked" : "";
      echo "<div class='order-item'>
              <strong>{$label}:</strong>
              <div><input type='checkbox' data-id='{$order->id}' data-field='{$field}' {$checked}></div>
            </div>";
    } else {
      // Nếu là ô có thể chỉnh sửa
      $editable = in_array($field, $editable_fields) ? "contenteditable='true'" : "";
      $class = in_array($field, $editable_fields) ? "class='editable'" : "";
      echo "<div class='order-item'>
              <strong>{$label}:</strong>
              <div {$editable} {$class} data-id='{$order->id}' data-field='{$field}'>{$value}</div>
            </div>";
    }
  }

  echo "</div>";

  echo "<table class='w-100 mt-4 table-list-chi-tiet' style='width: 100%'>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Cửa hàng</th>
                    <th>Số lượng</th>
                    <th style='width: 120px;'>Giá tiền</th>
                </tr>
            </thead>
            <tbody>";

  foreach ($carts as $cart) {
    // todo
    echo "<tr>
            <td>
                <div class='d-flex align-items-center gap-2'>
                    <img width='40px' src='{$cart->product_image}' />
                    <a href='{$cart->product_url}'>" . parse_url($cart->product_url, PHP_URL_HOST) . "</a>
                </div>
                
            </td>
            <td>
                <a href='{$cart->shop_url}'>{$cart->shop_id}</a>
            </td>
            <td>
                <input " . ($order->status > 1 ? "disabled" : "") . " data-type='quantity-cart' data-item='{$cart->id}' value='{$cart->quantity}' />
            </td>
            <td>" . format_price_vnd($order->exchange_rate * $cart->price) . "</td>
          </tr>";
  }

  echo "</tbody></table>";
    echo "<div class='order-item'>
    <strong>Trạng thái:</strong>
    <select id='statusDropdown' data-id='{$order->id}'>
      <option value='2' " . ($order->status == 2 ? "selected" : "") . ">Đang mua hàng</option>
      <option value='3' " . ($order->status == 3 ? "selected" : "") . ">Đã mua hàng</option>
      <option value='4' " . ($order->status == 4 ? "selected" : "") . ">NCC phát hàng</option>
      <option value='5' " . ($order->status == 5 ? "selected" : "") . ">Nhập kho TQ</option>
      <option value='6' " . ($order->status == 6 ? "selected" : "") . ">Nhập kho VN</option>
      <option value='7' " . ($order->status == 7 ? "selected" : "") . ">Khách nhận hàng</option>
    </select>
  </div>";
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

        $("input[data-type='quantity-cart']").each(function () {
          updates.push({
            cart_id: $(this).data("item"),
            field: "quantity",
            value: $(this).val().trim() || null
          });
        });

        $("input[type='checkbox']").each(function () {
          updates.push({
            order_id: $(this).data("id"),
            field: $(this).data("field"),
            value: $(this).is(":checked") ? 1 : 0
          });
        });

        const statusFieldMap = {
          2: "ngay_dat_coc",
          3: "da_mua_hang",
          4: "ngay_nhap_kho_tq",
          5: "ngay_nhap_kho_tq",
          6: "ngay_nhap_kho_vn",
          7: "ngay_nhan_hang"
        };

        const selectedStatus = $("#statusDropdown").val();
        const orderId = $("#statusDropdown").data("id");
        updates.push({
          order_id: orderId,
          field: "status",
          value: selectedStatus
        });

        if (statusFieldMap[selectedStatus]) {
          updates.push({
            order_id: orderId,
            field: statusFieldMap[selectedStatus],
            value: new Date().toISOString().slice(0, 19).replace('T', ' ')
          });
        }

        $.ajax({
          url: '<?php echo admin_url("admin-ajax.php"); ?>',
          type: "POST",
          data: {
            action: "update_order_admin",
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
