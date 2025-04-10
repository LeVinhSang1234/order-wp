<?php
if (!defined('ABSPATH')) {
  exit; // Ngăn truy cập trực tiếp
}

// 🔹 Thêm menu "Đơn hàng" vào Admin
function add_custom_admin_menu()
{
  add_menu_page(
    'Quản lý đơn hàng',
    'Đơn hàng',
    'manage_options',
    'custom_orders',
    'render_order_page',
    'dashicons-cart',
    25
  );

  add_menu_page(
    'Nạp Tiền',
    'Nạp Tiền',
    'manage_options',
    'nap-tien',
    'render_nap_tien_page',
    'dashicons-money-alt',
    26
  );

  add_submenu_page(
    'custom_orders',
    'Thêm đơn hàng',
    'Thêm mới',
    'manage_options',
    'add_order',
    'render_add_order_page'
  );
}
add_action('admin_menu', 'add_custom_admin_menu');

// 🔹 Hàm hiển thị danh sách đơn hàng
function render_order_page()
{
  global $wpdb;

  // Get the selected status, type, and search query from the query string
  $status_filter = isset($_GET['status_filter']) ? intval($_GET['status_filter']) : 0;
  $type_filter = isset($_GET['type_filter']) ? intval($_GET['type_filter']) : -1;
  $search_order_id = isset($_GET['search_order_id']) ? intval($_GET['search_order_id']) : 0;

  // Calculate totals for each status
  $status_totals = $wpdb->get_results("SELECT status, COUNT(*) as total FROM {$wpdb->prefix}orders GROUP BY status", OBJECT_K);
  $totals = [];
  foreach ($status_totals as $status => $data) {
    $totals[intval($status)] = intval($data->total);
  }

  $status_str = ["", "Chờ báo giá", "Đang mua hàng", "Đã mua hàng", "NCC phát hàng", "Nhập kho TQ", "Nhập kho VN", "Khách nhận hàng", "Đơn hàng hủy", "Đơn khiếu nại"];
  $status_colors = ["", "black", "orange", "green", "blue", "purple", "crimson", "teal", "red", "gray"];

  // Render filter UI
  echo '<div class="wrap"><h2>Danh sách đơn hàng</h2>';
  echo '<form method="get" style="margin-bottom: 20px;">';
  echo '<input type="hidden" name="page" value="custom_orders">';
  echo '<div style="display: flex; gap: 10px; align-items: center;">';
  echo '<input style="height: 31px" type="number" name="search_order_id" placeholder="Tìm theo mã đơn hàng" value="' . ($search_order_id ?: '') . '" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;">';
  echo '<button type="submit" class="button button-primary">Tìm kiếm</button>';
  echo '</div>';
  echo '</form>';

  // Render type filter buttons
  echo '<div class="type-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; margin-top: 10px;">';
  echo '<a class="type-tab ' . ($type_filter === -1 ? 'active' : '') . '" href="' . admin_url('admin.php?page=custom_orders&status_filter=' . $status_filter) . '">Tất cả loại</a>';
  echo '<a class="type-tab ' . ($type_filter === 0 ? 'active' : '') . '" href="' . admin_url('admin.php?page=custom_orders&status_filter=' . $status_filter . '&type_filter=0') . '">Đơn hàng order</a>';
  echo '<a class="type-tab ' . ($type_filter === 1 ? 'active' : '') . '" href="' . admin_url('admin.php?page=custom_orders&status_filter=' . $status_filter . '&type_filter=1') . '">Đơn ký gửi</a>';
  echo '</div>';

  echo '<div class="status-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; margin-top: 10px;">';
  echo '<a class="status-tab ' . ($status_filter === 0 ? 'active' : '') . '" href="' . admin_url('admin.php?page=custom_orders') . '">Tất cả (' . array_sum($totals) . ')</a>';
  foreach ($status_str as $key => $label) {
    if ($key === 0) continue;
    $active_class = $status_filter === $key ? 'active' : '';
    $count = isset($totals[$key]) ? $totals[$key] : 0;
    $color = $status_colors[$key];
    echo '<a class="status-tab ' . $active_class . '" href="' . admin_url('admin.php?page=custom_orders&status_filter=' . $key) . '" style="color: ' . $color . ';">' . $label . ' (' . $count . ')</a>';
  }
  echo '</div>';

  // Fetch orders based on the selected status, type, and search query
  $query = "SELECT * FROM {$wpdb->prefix}orders WHERE 1=1";
  if ($status_filter > 0) {
    $query .= $wpdb->prepare(" AND status = %d", $status_filter);
  }
  if ($type_filter >= 0) {
    $query .= $wpdb->prepare(" AND type = %d", $type_filter);
  }
  if ($search_order_id > 0) {
    $query .= $wpdb->prepare(" AND id = %d", $search_order_id);
  }
  $query .= " ORDER BY created_at DESC";
  $orders = $wpdb->get_results($query);

  // Render orders table
  echo '<table class="wp-list-table widefat fixed striped">';
  echo '<thead>
          <tr>
              <th style="color: white">ID</th>
              <th style="color: white">Trạng thái</th>
              <th style="color: white">Email</th>
              <th style="color: white">Điện thoại</th>
              <th style="color: white">Địa chỉ</th>
              <th style="color: white">Vận đơn</th>
              <th style="color: white">Thương hiệu</th>
              <th style="color: white">Thanh toán</th>
              <th style="color: white">Ngày tạo</th>
              <td style="color: white">Xem Chi tiết</td>
          </tr>
      </thead><tbody>';

  foreach ($orders as $order) {
    $detail_url = admin_url("admin.php?page=order_detail&id={$order->id}");
    $status_display = $status_str[intval($order->status)];
    $status_color = '';
    switch (intval($order->status)) {
      case 1:
        $status_color = 'color: black;'; // Chờ báo giá
        break;
      case 2:
        $status_color = 'color: orange;'; // Đang mua hàng
        break;
      case 3:
        $status_color = 'color: green;';  // Đã mua hàng
        break;
      case 4:
        $status_color = 'color: blue;';   // NCC phát hàng
        break;
      case 5:
        $status_color = 'color: purple;'; // Nhập kho TQ
        break;
      case 6:
        $status_color = 'color: crimson;';   // Nhập kho VN
        break;
      case 7:
        $status_color = 'color: teal;'; // Khách nhận hàng
        break;
      case 8:
        $status_color = 'color: red;';    // Đơn hàng hủy
        break;
      case 9:
        $status_color = 'color: gray;';   // Đơn khiếu nại
        break;
    }
    $id_display = $order->type == 1 ?  $order->id . "-(Đơn ký gửi)" : $order->id;
    echo "<tr data-id='{$order->id}'>
          <td><a href='{$detail_url}'>{$id_display}</a></td>
          <td contenteditable='false' class='editable' data-field='status' style='{$status_color} font-weight: bold'>{$status_display}</td>
          <td contenteditable='false' class='editable' data-field='email'>{$order->email}</td>
          <td contenteditable='false' class='editable' data-field='phone'>{$order->phone}</td>
          <td contenteditable='false' class='editable' data-field='address'>{$order->address}</td>
          <td contenteditable='false' class='editable' data-field='van_don'>{$order->van_don}</td>
          <td contenteditable='false' class='editable' data-field='thuong_hieu'>{$order->thuong_hieu}</td>
          <td contenteditable='false' class='editable' data-field='da_thanh_toan'>{$order->da_thanh_toan}</td>
          <td>{$order->created_at}</td>
          <td><a href='{$detail_url}'>(Xem Chi tiết)</a></td>
      </tr>";
  }

  echo '</tbody></table></div>';

  // Gắn script AJAX
?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".editable").forEach(cell => {
        cell.addEventListener("blur", function() {
          let orderId = this.closest("tr").dataset.id;
          let field = this.dataset.field;
          let value = this.innerText;

          fetch(ajaxurl, {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded"
              },
              body: new URLSearchParams({
                action: "update_order",
                order_id: orderId,
                field: field,
                value: value
              })
            }).then(response => response.json())
            .then(data => {
              if (!data.success) {
                alert("Cập nhật thất bại!");
              }
            });
        });
      });
    });
  </script>
  <style>
    .status-tabs {
      margin: 10px 0;
      width: 100%;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
    }

    .status-tab {
      padding: 4px 8px;
      border: 1px solid #ddd;
      border-radius: 16px;
      background: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-bottom: 5px;
      padding: 4px 16px;
      font-size: 14px;
      font-weight: 700;
    }

    .status-tab:hover {
      background: #f5f5f5;
    }

    .status-tab.active {
      background: rgba(0, 123, 255, 0.48);
      color: white;
      border-color: #007bff;
    }

    /* Add styles for type filter buttons */
    .type-tabs {
      margin: 10px 0;
      width: 100%;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
    }

    .type-tab {
      padding: 4px 8px;
      border: 1px solid #ddd;
      border-radius: 16px;
      background: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-bottom: 5px;
      padding: 4px 16px;
      font-size: 14px;
      font-weight: 700;
    }

    .type-tab:hover {
      background: #f5f5f5;
    }

    .type-tab.active {
      background: rgba(0, 123, 255, 0.48);
      color: white;
      border-color: #007bff;
    }
  </style>
<?php
}

// 🔹 Hàm hiển thị UI "Thêm đơn hàng"
function render_add_order_page()
{
?>
  <div class="wrap">
    <h2>Thêm Đơn Hàng Mới</h2>
    <form method="post">
      <table class="form-table">
        <tr>
          <th><label for="user_id">ID Người dùng</label></th>
          <td><input type="number" name="user_id" required class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="status">Trạng thái</label></th>
          <td>
            <select name="status">
              <option value="1">Đang xử lý</option>
              <option value="2">Hoàn thành</option>
              <option value="3">Đã hủy</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="ho_ten">Họ Tên</label></th>
          <td><input type="text" name="ho_ten" class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="email">Email</label></th>
          <td><input type="email" name="email" class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="phone">Số điện thoại</label></th>
          <td><input type="text" name="phone" class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="address">Địa chỉ</label></th>
          <td><input type="text" name="address" class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="so_kien_hang">Số kiện hàng</label></th>
          <td><input type="number" name="so_kien_hang" class="regular-text"></td>
        </tr>
        <tr>
          <th><label for="da_thanh_toan">Thanh toán</label></th>
          <td><input type="number" step="0.01" name="da_thanh_toan" class="regular-text"></td>
        </tr>
      </table>
      <input type="submit" name="submit_order" class="button button-primary" value="Thêm đơn hàng">
    </form>
  </div>
<?php

  // 🔹 Xử lý thêm đơn hàng khi submit
  if (isset($_POST['submit_order'])) {
    global $wpdb;
    $wpdb->insert(
      "{$wpdb->prefix}orders",
      [
        'user_id' => intval($_POST['user_id']),
        'cart_ids' => sanitize_text_field([]),
        'status' => intval($_POST['status']),
        'ho_ten' => sanitize_text_field($_POST['ho_ten']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'address' => sanitize_text_field($_POST['address']),
        'so_kien_hang' => intval($_POST['so_kien_hang']),
        'da_thanh_toan' => floatval($_POST['da_thanh_toan']),
      ],
      ['%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%f']
    );

    echo '<div class="updated"><p>Đơn hàng đã được thêm thành công!</p></div>';
  }
}
?>