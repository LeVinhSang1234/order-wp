<?php
// Thêm menu vào WordPress Admin
function add_orders_support_menu() {
  add_menu_page(
      'Yêu cầu thanh toán hộ', 
      'Yêu cầu thanh toán hộ', 
      'manage_options', 
      'orders-support', 
      'render_orders_support_page',
      'dashicons-money-alt', 
      27
  );
}
add_action('admin_menu', 'add_orders_support_menu');

// Hiển thị danh sách yêu cầu thanh toán hộ
function render_orders_support_page() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'orders_support'; // Bảng orders_support
  $orders = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

  echo '<div class="wrap"><h2>Danh sách yêu cầu thanh toán hộ</h2>';
  echo '<table class="widefat fixed">';
  echo '<thead><tr>
      <th>Order ID</th>
      <th>Phí dịch vụ</th>
      <th>Tổng tiền</th>
      <th>Ghi chú</th>
      <th>Trạng thái</th>
      <th>Hành động</th>
  </tr></thead>';
  echo '<tbody>';

  foreach ($orders as $order) {
      echo '<tr>';
      echo '<td>' . esc_html($order->order_id) . '</td>';
      echo '<td>' . esc_html(number_format($order->phi_dich_vu, 0, ',', '.')) . ' VND</td>';
      echo '<td>' . esc_html(number_format($order->tong_tien, 0, ',', '.')) . ' VND</td>';
      echo '<td>' . esc_html($order->ghi_chu) . '</td>';
      echo '<td>' . ($order->trang_thai == 1 ? '<span style="color: green;">Đã duyệt</span>' : '<span style="color: red;">Chưa xử lý</span>') . '</td>';
      echo '<td>';
      if ($order->trang_thai == 0) {
          echo '<form method="POST" action="' . admin_url('admin-post.php') . '">
              <input type="hidden" name="action" value="approve_order_support">
              <input type="hidden" name="order_id" value="' . esc_attr($order->order_id) . '">
              <input type="hidden" name="_wpnonce" value="' . wp_create_nonce('approve_order_support') . '">
              <button type="submit" class="button button-primary">Duyệt</button>
          </form>';
      } else {
          echo '<span style="color:green;">Đã duyệt</span>';
      }
      echo '</td>';
      echo '</tr>';
  }

  echo '</tbody></table></div>';
}

// Xử lý hành động duyệt đơn hàng
function handle_approve_order_support() {
  if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'approve_order_support')) {
      wp_die('Xác thực không hợp lệ!');
  }

  if (!current_user_can('manage_options')) {
      wp_die('Bạn không có quyền thực hiện hành động này.');
  }

  global $wpdb;
  $table_name = $wpdb->prefix . 'orders_support';
  $order_id = intval($_POST['order_id']);

  if ($order_id) {
      $wpdb->update(
          $table_name,
          ['trang_thai' => 1], // Cập nhật trạng thái thành "Đã duyệt"
          ['order_id' => $order_id],
          ['%d'],
          ['%d']
      );
  }

  wp_redirect(admin_url('admin.php?page=orders-support'));
  exit;
}
add_action('admin_post_approve_order_support', 'handle_approve_order_support');

