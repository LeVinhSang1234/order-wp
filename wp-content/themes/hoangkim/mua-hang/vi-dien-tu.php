<?php
global $wpdb;
$user_id = get_current_user_id();

$time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

$ma_phieu_thu = isset($_GET['ma_phieu_thu']) ? sanitize_text_field($_GET['ma_phieu_thu']) : '';

$query = "SELECT * FROM {$wpdb->prefix}wallet_transaction WHERE user_id = %d";
$params = [$user_id];

if (!empty($time_from)) {
  $query .= " AND created_at >= %s";
  $params[] = $time_from;
}

if (!empty($time_to)) {
  $query .= " AND created_at <= %s";
  $params[] = $time_to;
}

if (!empty($status)) {
  $query .= " AND status LIKE %s";
  $params[] = '%' . $wpdb->esc_like($type) . '%';
}

if (!empty($ma_phieu_thu)) {
  $query .= " AND ma_phieu_thu = %s";
  $params[] = $ma_phieu_thu;
}

$query .= " ORDER BY created_at DESC";
$wallets = $wpdb->get_results($wpdb->prepare($query, ...$params));
$tien = trim(display_user_wallet());

// Fetch transaction history related to balance changes
$wallet_transactions = $wpdb->get_results($wpdb->prepare(
  "SELECT *, 'wallet' AS source FROM {$wpdb->prefix}wallet_transaction WHERE user_id = %d AND da_xu_ly = 1",
  $user_id
));

$order_transactions = $wpdb->get_results($wpdb->prepare(
  "SELECT *, 'order' AS source FROM {$wpdb->prefix}history_orders_transaction WHERE user_id = %d",
  $user_id
));

// Merge and sort transactions by date
$transactions = array_merge($wallet_transactions, $order_transactions);
usort($transactions, function ($a, $b) {
  return strtotime($b->created_at) - strtotime($a->created_at);
});

$time_from_history = isset($_GET['time_from_history']) ? sanitize_text_field($_GET['time_from_history']) : '';
$time_to_history = isset($_GET['time_to_history']) ? sanitize_text_field($_GET['time_to_history']) : '';

if (!empty($time_from_history) || !empty($time_to_history)) {
  $transactions = array_filter($transactions, function ($transaction) use ($time_from_history, $time_to_history) {
    $transaction_date = strtotime($transaction->created_at);
    $from_date = !empty($time_from_history) ? strtotime($time_from_history) : null;
    $to_date = !empty($time_to_history) ? strtotime($time_to_history) : null;

    if ($from_date && $transaction_date < $from_date) {
      return false;
    }
    if ($to_date && $transaction_date > $to_date) {
      return false;
    }
    return true;
  });
}

$tong_nap = $wpdb->get_var($wpdb->prepare(
  "SELECT SUM(so_tien) FROM {$wpdb->prefix}wallet_transaction WHERE user_id = %d AND da_xu_ly = 1",
  $user_id
));

$tong_chi_tieu = $wpdb->get_var($wpdb->prepare(
  "SELECT SUM(da_thanh_toan) FROM {$wpdb->prefix}orders WHERE user_id = %d AND  status != 7",
  $user_id
));

$tong_dang_coc = $wpdb->get_var($wpdb->prepare(
  "SELECT SUM(da_thanh_toan) FROM {$wpdb->prefix}orders WHERE user_id = %d AND status = 2",
  $user_id
));

// Calculate total order amount excluding status = 7
$query = "SELECT * FROM {$wpdb->prefix}orders WHERE user_id = %d AND status != 7";
$orders = $wpdb->get_results($wpdb->prepare($query, $user_id));

$total_order_amount = 0;
foreach ($orders as $order) {
  $cart_ids = json_decode($order->cart_ids, true) ?: [];
  $carts = [];

  if (!empty($cart_ids)) {
    $cart_query = $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}cart WHERE id IN (" . implode(',', array_fill(0, count($cart_ids), '%d')) . ")",
      ...$cart_ids
    );
    $carts = $wpdb->get_results($cart_query);
  }

  $exchange_rate = $order->exchange_rate ?: floatval(get_option('exchange_rate', 1.0));
  $total_price = array_reduce($carts, fn($sum, $cart) => $sum + $cart->price * $cart->quantity, 0);

  $total_order_amount += ($total_price * $exchange_rate) +
    ($order->phi_ship_noi_dia * $exchange_rate) +
    $order->phi_kiem_dem +
    ($order->phi_gia_co * $exchange_rate) +
    ($order->chiet_khau_dich_vu * $exchange_rate);
}

$can_thanh_toan = $total_order_amount -  $tong_chi_tieu;
?>

<div class="dashboard">
  <div class="d-flex flex-column flex-md-row w-100 gap-2">
    <div class="mt-3 flex-3 align-items-stretch">
      <h4 class="text-uppercase">Ví điện tử</h4>
      <div class="notification-dashboard" style="height: 150px">
        <div class="d-flex align-items-center justify-content-between">
          <div>Số dư trong ví: <strong
              style="color: #ff0000"><?php echo format_price_vnd(intval($tien ?? 0)) ?></strong></div>
          <a target="__bla;
nk" href="<?php echo site_url() . '/nap-tien' ?>" class="btn btn-primary">Nạp
            tiền</a>
        </div>
        <div>Mã nạp tiền: <strong
            style="color: #ff0000">HK-MS<?php echo sprintf('%02d', get_current_user_id()); ?></strong>
        </div>
        <!-- <div style="font-size: 12px" class="mt-2">
          Tổng tiền hàng đã về chờ tất toán : <strong style="color: #ff0000">0</strong> đ
        </div>
        <div style="font-size: 12px">Tổng tiền hàng chưa về : <strong style="color: #ff0000">0</strong> đ</div> -->
      </div>
    </div>
    <div class="mt-3 flex-1">
      <h4 class="text-uppercase">Tài chính</h4>
      <div class="notification-dashboard" style="height: 150px">
        <div class="mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền nạp : <span><strong
              style="color: green"><?php echo format_price_vnd(intval($tong_nap ?? 0)); ?></strong></span>
        </div>
        <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng chi tiêu :
          <span><strong
              style="color: #ff0000"><?php echo format_price_vnd(intval($tong_chi_tieu ?? 0)); ?></strong></span>
        </div>
        <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền đơn
          hàng : <span><strong
              style="color: #ff0000"><?php echo format_price_vnd(intval($total_order_amount ?? 0)); ?></strong></span>
        </div>
        <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tiền đang cọc :
          <span><strong
              style="color: #ff0000"><?php echo format_price_vnd(intval($tong_dang_coc ?? 0)); ?></strong></span>
        </div>
        <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Cần thanh toán :
          <span><strong
              style="color: #ff0000"><?php echo format_price_vnd(intval($can_thanh_toan ?? 0)); ?></strong></span>
        </div>
        <div class="border-dotted"></div>
      </div>
    </div>
  </div>
  <div class="mt-3 flex-1">
    <h4 class="text-uppercase">Nạp tiền vào ví điện tử</h4>
    <div class="notification-dashboard">
      <div class="d-flex flex-column flex-md-row gap-4">
        <img style="max-height: 350px; width: auto; object-fit: contain;"
          src="<?php echo get_template_directory_uri() . '/images/bank.png' ?>" />
        <div>
          <h6>MB NGÂN HÀNG QUÂN ĐỘI</h6>
          <div>Số tài khoản: <strong>868199533333</strong></div>
          <div>Chủ tài khoản: <strong>Lê Kim Trường</strong></div>
          <div>Nội dung chuyển khoản: <strong
              style="color: green">MS<?php echo sprintf('%02d', get_current_user_id()); ?></strong></div>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-5">
    <h4 class="text-uppercase">Lịch sử biến động số dư</h4>
    <div class="notification-dashboard">
      <div class="d-flex align-items-center flex-wrap gap-2">
        <?php
        $id = "time_from_history";
        $placeholder = "Từ";
        include get_template_directory() . '/mua-hang/input-date-picker.php';
        ?>
        <?php
        $id = "time_to_history";
        $placeholder = "Đến";
        include get_template_directory() . '/mua-hang/input-date-picker.php';
        ?>
        <button id="find-2" class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
      </div>
      <div class="table-responsive scrollable-table mt-3">
        <table class="w-100 mt-2" style="min-width: 1200px;">
          <thead>
            <tr>
              <th>STT</th>
              <th>Loại giao dịch</th>
              <th>Mã đơn hàng</th>
              <th>Số tiền</th>
              <th>Ngày giao dịch</th>
              <th>Người tạo</th>
              <th>Ghi chú</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($transactions as $key => $transaction) { ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $transaction->source === 'wallet' ? 'Nạp tiền' : 'Cọc đơn hàng'; ?></td>
                <td>
                  <?php if ($transaction->source === 'order') { ?>
                    MS<?php echo str_pad($user_id, 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($transaction->order_id, 2, '0', STR_PAD_LEFT); ?>
                  <?php } ?>
                </td>
                <td style="color: <?php echo $transaction->source === 'wallet' ? 'green' : 'red'; ?>;">
                  <?php echo $transaction->source === 'wallet' ? '+' : '-'; ?>
                  <?php echo format_price_vnd($transaction->so_tien); ?>
                </td>
                <td><?php echo date('d/m/Y H:i:s', strtotime($transaction->created_at)); ?></td>
                <td>
                  <?php 
                  if (!empty($transaction->nguoi_thuc_hien)) {
                    $user_info = get_userdata($transaction->nguoi_thuc_hien);
                    if ($user_info) {
                      echo in_array('administrator', $user_info->roles) ? 'Administrator' : $user_info->user_email;
                    } else {
                      echo 'N/A';
                    }
                  } else {
                    echo 'N/A';
                  }
                  ?>
                </td>
                <td>
                  <?php echo $transaction->source === 'wallet' ? $transaction->ghi_chu : $transaction->hinh_thuc; ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>


<script>
  $(document).ready(function() {
    const params = new URLSearchParams(window.location.search);

    if (params.has('time_from')) $('#time_from').val(params.get('time_from').replace(/\//g, '-'));
    if (params.has('time_to')) $('#time_to').val(params.get('time_to').replace(/\//g, '-'));
    if (params.has('status')) $('#status').val(params.get('status'));
    if (params.has('ma_phieu_thu')) $('#ma_phieu_thu').val(params.get('ma_phieu_thu'));

    $('.btn-find').on('click', function(event) {
      event.stopPropagation();

      const formatDate = (dateStr) => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        if (isNaN(date)) return '';
        return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' +
          String(date
            .getDate()).padStart(2, '0');
      };

      const time_from = formatDate($('#time_from').val());
      const time_to = formatDate($('#time_to').val());
      const ma_phieu_thu = $('#ma_phieu_thu').val();
      const type = $('#status').val();


      let url = new URL(window.location.href);
      let params = url.searchParams;

      if (time_from) params.set('time_from', time_from);
      else params.delete('time_from');

      if (time_to) params.set('time_to', time_to);
      else params.delete('time_to');

      if (type) params.set('status', type.toUpperCase());
      else params.delete('status');

      if (ma_phieu_thu) params.set('ma_phieu_thu', ma_phieu_thu);
      else params.delete('ma_phieu_thu');

      window.history.pushState({}, '', url.pathname + '?' + params.toString());
      window.location.reload();
    });

    $('#find-2').on('click', function(event) {
      event.stopPropagation();

      const formatDate = (dateStr) => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        if (isNaN(date)) return '';
        return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' +
          String(date
            .getDate()).padStart(2, '0');
      };

      const time_from_history = formatDate($('#time_from_history').val());
      const time_to_history = formatDate($('#time_to_history').val());

      let url = new URL(window.location.href);
      let params = url.searchParams;

      if (time_from_history) params.set('time_from_history', time_from_history);
      else params.delete('time_from_history');

      if (time_to_history) params.set('time_to_history', time_to_history);
      else params.delete('time_to_history');

      window.history.pushState({}, '', url.pathname + '?' + params.toString());
      window.location.reload();
    });
  })
</script>
``` 