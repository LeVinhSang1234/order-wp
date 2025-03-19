<?php
// Kết nối với database
global $wpdb;
$table_name = $wpdb->prefix . 'orders';
$user_id = get_current_user_id();
$orders = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC");

// Lấy tỷ giá và phí mặc định
$exchange_rate = floatval(get_option('exchange_rate', 1.0));
$phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));

// Xử lý khi người dùng gửi yêu cầu
if (isset($_POST['submit_don_thanh_toan_ho']) && is_user_logged_in()) {
  $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
  $exchange_rate = isset($_POST['ty_gia']) ? floatval($_POST['ty_gia']) : 1.0;
  $service_fee = isset($_POST['phi_dich_vu']) ? floatval($_POST['phi_dich_vu']) : 0;
  $note = isset($_POST['ghi_chu']) ? sanitize_text_field($_POST['ghi_chu']) : '';

  $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $order_id));

  if ($order) {
    $wpdb->update(
      $table_name,
      [
        'user_id' => $user_id,
        'status' => 'pending',
        'note' => $note,
        'exchange_rate' => $exchange_rate,
        'phi_mua_hang' => $service_fee
      ],
      ['id' => $order_id, 'user_id' => $user_id],
      ['%d', '%s', '%s', '%f', '%f', '%s']
    );
    echo "<script>alert('Tạo yêu cầu thành công!');window.location.href='/don-thanh-toan-ho/'</script>";
    exit;
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
          <select class="w-filter-full" name="order_id" id="orderSelect"
            style="width: 100%; max-width: 600px">
            <?php foreach ($orders as $order_item) : ?>
              <option value="<?php echo esc_attr($order_item->id); ?>"
                data_ty_gia="<?php echo esc_attr($order_item->exchange_rate ?: $exchange_rate); ?>"
                data-total_amount="<?php echo esc_attr(($order_item->gia_tien * $exchange_rate) + $phi_mua_hang); ?>"
                data-phi_dich_vu="<?php echo esc_attr($order_item->exchange_rate ?: $exchange_rate); ?>">
                <?php echo "HK_" . sprintf('%02d', esc_html($order_item->id)); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-flex align-items-center fs-13 gap-3 w-100">
          <strong class="mt-1" style="width: 200px; text-align: right"></strong>
          <div style="width: 100%; max-width: 600px;">
            <span>Tổng tiền thanh toán: </span>
            <span class="text-danger fw-bold" id="total_amount">0</span>
          </div>
        </div>
        <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right">Tỷ giá:</strong>
          <input readonly disabled value="<?php echo esc_attr($exchange_rate); ?>" require type="number"
            name="ty_gia" placeholder="Tỷ giá..." required style="width: 100%; max-width: 600px" />
        </div>
        <div class="mt-3 d-flex fs-13 gap-3 w-100">
          <strong style="width: 200px; text-align: right">Phí dịch vụ:</strong>
          <input require type="number" name="phi_dich_vu" placeholder="Phí dịch vụ..." required
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
    const orderSelect = document.getElementById("orderSelect");
    if (!orderSelect) return;

    const exchangeRateInput = document.querySelector("input[name='ty_gia']");
    const serviceFeeInput = document.querySelector("input[name='phi_dich_vu']");
    const totalAmountText = document.getElementById("total_amount");

    function updateFields() {
      const selectedOption = orderSelect.options[orderSelect.selectedIndex];
      if (!selectedOption) return;

      const exchangeRate = parseFloat(selectedOption.getAttribute("data_ty_gia")) || 0;
      const totalAmount = parseFloat(selectedOption.getAttribute("data-total_amount")) || 0;
      const serviceFee = parseFloat(serviceFeeInput.value) || 0;

      if (exchangeRateInput) exchangeRateInput.value = exchangeRate;
      if (totalAmountText) totalAmountText.textContent = (totalAmount + serviceFee).toLocaleString() + "đ";
    }

    orderSelect.addEventListener("change", updateFields);
    updateFields();
  })
</script>