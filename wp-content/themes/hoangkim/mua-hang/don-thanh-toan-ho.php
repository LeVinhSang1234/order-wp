<?php
// Kết nối với database
global $wpdb;
$table_order = $wpdb->prefix . 'orders';
$user_id = get_current_user_id();
$orders = $wpdb->get_results("SELECT * FROM {$table_order} ORDER BY created_at DESC");

// Lấy tỷ giá và phí mặc định
$rate = floatval(get_option('exchange_rate', 1.0));
$phi_dich_vu = floatval(get_option('phi_dich_vu', 1.0));

// Xử lý khi người dùng gửi yêu cầu
if (isset($_POST['submit_don_thanh_toan_ho']) && is_user_logged_in()) {
  $table_name = $wpdb->prefix . 'orders_support';
    
  $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
  $exchange_rate = isset($_POST['ty_gia']) ? floatval($_POST['ty_gia']) : 1.0;
  $service_fee = isset($_POST['phi_dich_vu']) ? floatval($_POST['phi_dich_vu']) : 0;
  $note = isset($_POST['ghi_chu']) ? sanitize_text_field($_POST['ghi_chu']) : '';
  $total_amount = isset($_POST['tong_tien']) ? floatval($_POST['tong_tien']) : 0;

  // Chèn dữ liệu mới vào bảng orders_support
  // Chèn dữ liệu mới vào bảng orders_support
  $result = $wpdb->insert(
    $table_name,
    [
        'order_id' => $order_id,
        'trang_thai' => 0, // Mặc định trạng thái là 0 (chưa xử lý)
        'phi_dich_vu' => $service_fee,
        'ghi_chu' => $note,
        'tong_tien' => $total_amount, // Lưu tổng tiền vào DB
        'created_at' => current_time('mysql') // Lấy thời gian hiện tại
    ],
    ['%d', '%d', '%f', '%s', '%f', '%s']
);

if ($result) {
  echo "<script>alert('Tạo yêu cầu thành công!');window.location.href='/don-thanh-toan-ho/'</script>";
  exit;
} else {
  echo "<script>alert('Lỗi khi tạo yêu cầu!');</script>";
}
}
?>

<div class="dashboard">
  <div class="mt-3 flex-1">
    <h4 class="text-uppercase">TẠO YÊU CẦU THANH TOÁN HỘ</h4>
    <div class="notification-dashboard">
      <form class="w-100" method="post" action="" enctype="multipart/form-data">
        <div class="d-flex align-items-center fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right">Chọn đơn hàng:</strong>
          <select class="w-filter-full" name="order_id" id="orderSelect" style="width: 100%; max-width: 600px">
            <?php foreach ($orders as $order_item) : 
                // Kiểm tra cart_ids trước khi decode
                $cart_ids_array = !empty($order_item->cart_ids) ? json_decode($order_item->cart_ids, true) : [];

                if (!is_array($cart_ids_array)) {
                    $cart_ids_array = []; // Đảm bảo biến này luôn là một mảng
                }

                // Kiểm tra nếu có dữ liệu thì mới chạy query
                if (!empty($cart_ids_array)) {
                    $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
                    $query = $wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders) limit 1",
                        ...$cart_ids_array
                    );
                    $carts = $wpdb->get_results($query);
                } else {
                    $carts = [];
                }

                $image_url = isset($carts[0]->product_image) ? $carts[0]->product_image : '';

                // Xử lý các biến có thể NULL
                $exchange_rate = $order_item-> exchange_rate ?? $rate ?? 1;
                $phi_mua_hang = $phi_mua_hang ?? 0;
                $phi_dich_vu = $phi_dich_vu ?? 0;

                // Tính tổng giá trị đơn hàng
                $total = 0;
                foreach ($carts as $cart) {
                    $total += floatval($cart->price ?? 0);
                }
                $total = $total * $exchange_rate;
                $total += floatval($phi_mua_hang); // Cộng phí mua hàng

                // Lấy ngày tạo đơn hàng
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $order_item->created_at);
                $formatted_date = $date ? $date->format('d/m/Y') : '';
            ?>
            <option value="<?php echo esc_attr($order_item->id); ?>"
              data-ty-gia="<?php echo esc_attr($exchange_rate); ?>" data-total-amount="<?php echo esc_attr($total); ?>"
              data-phi-dich-vu="<?php echo esc_attr($phi_dich_vu); ?>"
              data-image-url="<?php echo esc_url($image_url); ?>"
              data-created-at="<?php echo esc_attr($formatted_date); ?>">
              <?php echo "HK_" . sprintf('%02d', esc_html($order_item->id)) . " - Tổng: " . number_format($total, 2) . " - Ngày: " . $formatted_date; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-flex align-items-center fs-13 gap-3 w-100">
          <strong class="mt-1" style="width: 200px; text-align: right"></strong>
          <div style="width: 100%; max-width: 600px;">
            <span>Tổng tiền thanh toán: </span>
            <span class="text-danger fw-bold" id="total_amount">
              0 </span>
          </div>
        </div>
        <input type="hidden" name="tong_tien" id="tong_tien">
        <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right">Tỷ giá:</strong>
          <input readonly disabled require type="number" name="ty_gia" placeholder="Tỷ giá..." required
            style="width: 100%; max-width: 600px" />
        </div>
        <div class="mt-3 d-flex fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right">Phí dịch vụ:</strong>
          <input readonly disabled require type="number" name="phi_dich_vu" placeholder="Phí dịch vụ..." required
            style="width: 100%; max-width: 600px" />
        </div>
        <div class="mt-3 d-flex fs-13 gap-3 w-100">
          <strong class="mt-1" style="width: 200px; text-align: right">Ghi chú:</strong>
          <textarea name="ghi_chu" placeholder="Ghi chú..." style="width: 100%; max-width: 600px"></textarea>
        </div>
        <div class="mt-3 d-flex fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right"></strong>
          <button type="submit" name="submit_don_thanh_toan_ho" class="btn btn-primary textleft">+ Gửi yêu
            cầu</button>
        </div>
      </form>
      <?php if (isset($_GET['success'])) : ?>
      <div class="alert alert-success">Yêu cầu thanh toán đã được gửi thành công!</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  const $orderSelect = $("#orderSelect");
  if ($orderSelect.length === 0) return;

  function updateFields() {
    const $selectedOption = $orderSelect.find(":selected");
    if ($selectedOption.length === 0) return;

    const exchangeRate = parseFloat($selectedOption.data("ty-gia")) || 0;
    const totalAmount = parseFloat($selectedOption.data("total-amount")) || 0;
    const serviceFee = parseFloat($selectedOption.data("phi-dich-vu")) || 0;
    const finalTotal = totalAmount + serviceFee;

    // console.log(exchangeRate);

    $("input[name='ty_gia']").val(exchangeRate);
    $("input[name='phi_dich_vu']").val((serviceFee * totalAmount) / 100);
    $("#total_amount").text((totalAmount + (serviceFee * totalAmount) / 100).toLocaleString() + "đ");
    $("input[name='tong_tien']").val(totalAmount + (serviceFee * totalAmount) / 100);
  }

  $orderSelect.on("change", updateFields);
  updateFields();
});
</script>