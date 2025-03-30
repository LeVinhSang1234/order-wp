<?php
global $wpdb;
$user_id = get_current_user_id();
$total_orders = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}orders WHERE user_id = %d",
    $user_id
));
$total_khieu_nai = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}orders WHERE user_id = %d AND status = 9",
    $user_id
));
$total_cart = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}cart WHERE user_id = %d and is_done = 0",
    $user_id
));

$time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

$query = "SELECT * FROM {$wpdb->prefix}notification WHERE user_id = %d";
$params = [$user_id];


if (!empty($time_from)) {
    $query .= " AND created_at >= %s";
    $params[] = $time_from;
}

if (!empty($time_to)) {
    $query .= " AND created_at <= %s";
    $params[] = $time_to;
}

if (!empty($type)) {
    $query .= " AND loai LIKE %s";
    $params[] = '%' . $wpdb->esc_like($type) . '%';
}
$query .= " ORDER BY is_read ASC, created_at DESC";
$notifications = $wpdb->get_results($wpdb->prepare($query, ...$params));

$tien = trim(display_user_wallet());
$wpdb->update(
    "{$wpdb->prefix}notification",
    array('is_read' => 1),
    array('user_id' => $user_id, 'is_read' => 0),
    array('%d'),
    array('%d', '%d')
);

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

?>

<style>
.scrollable-table {
  max-height: 1000px;
  /* Adjust height as needed */
  overflow-y: auto;
}
</style>

<div class="dashboard">
  <div class="row">
    <div class="col-lg-3 col-md-6 pb-2">
      <div class="box-dashboard box-dashboard-green">
        <h4><?php echo format_price_vnd(intval($tien ?? 0)) ?></h4>
        <div class="title">Số dư</div>
        <a href="/wallet" class="view-detail">Xem chi tiết</a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 pb-2">
      <div class="box-dashboard box-dashboard-aqua">
        <h4><?php echo $total_orders; ?> Đơn</h4>
        <div class="title">Đơn hàng</div>
        <a href="/don-hang" class="view-detail">Xem chi tiết</a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 pb-2">
      <div class="box-dashboard box-dashboard-cart">
        <h4><?php echo $total_cart; ?> sản phẩm</h4>
        <div class="title">Giỏ hàng</div>
        <a href="/gio-hang" class="view-detail">Xem chi tiết</a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 pb-2">
      <div class="box-dashboard box-dashboard-report">
        <h4><?php echo $total_khieu_nai ?></h4>
        <div class="title">Khiếu nại</div>
        <a href="/khieu-nai" class="view-detail">Xem chi tiết</a>
      </div>
    </div>
  </div>
  <div class="mt-3 flex-1">
    <h4 class="text-uppercase">Thông báo</h4>
    <div class="notification-dashboard">
      <div class="d-flex align-items-center flex-wrap gap-2">
        <?php
                $id = "time_from";
                $placeholder = "Từ";
                include get_template_directory() . '/mua-hang/input-date-picker.php';
                ?>
        <?php
                $id = "time_to";
                $placeholder = "Đến";
                include get_template_directory() . '/mua-hang/input-date-picker.php';
                ?>
        <select class="w-filter-full" name="type" id="type">
          <option>Loại thông báo</option>
          <option value="Ví điện tử">Ví điện tử</option>
          <option value="Đơn hàng">Đơn hàng</option>
          <option value="Khiếu nại">Khiếu nại</option>
          <option value="Vận đơn">Vận đơn</option>
          <option value="Tạo đơn hàng ký gửi">Tạo đơn hàng ký gửi</option>
        </select>
        <button id="find-1" class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
      </div>
      <div class="mt-3">
        Số thông báo: <strong><?php echo count($notifications) ?></strong>
        <div class="table-responsive scrollable-table">
          <table class="w-100 mt-2" style="min-width: 1000px;">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px">#</th>
                <th>Thời gian</th>
                <th>Loại thông báo</th>
                <th>Nội dung</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($notifications as $key => $notification) { ?>
              <tr class="<?php echo ($notification->is_read === '0' ? "no-read" : '') ?>">
                <td class="text-center"><?php echo $key + 1 ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?></td>
                <td><?php echo $notification->loai ?></td>
                <td><?php echo $notification->noi_dung ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
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
$('.no-read').on("mouseover", function() {
  $(this).removeClass('no-read')
})

$(document).ready(function() {

  const params = new URLSearchParams(window.location.search);

  if (params.has('time_from')) $('#time_from').val(params.get('time_from').replace(/\//g, '-'));
  if (params.has('time_to')) $('#time_to').val(params.get('time_to').replace(/\//g, '-'));
  if (params.has('time_from_history')) $('#time_from_history').val(params.get('time_from_history').replace(/\//g, '-'));
  if (params.has('time_to_history')) $('#time_to_history').val(params.get('time_to_history').replace(/\//g, '-'));
  if (params.has('type')) $('#type').val(params.get('type'));

  $('#find-1').on('click', function(event) {
    event.stopPropagation();

    const formatDate = (dateStr) => {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      if (isNaN(date)) return '';
      return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date
        .getDate()).padStart(2, '0');
    };

    const time_from = formatDate($('#time_from').val());
    const time_to = formatDate($('#time_to').val());
    const type = $('#type').val();


    let url = new URL(window.location.href);
    let params = url.searchParams;

    if (time_from) params.set('time_from', time_from);
    else params.delete('time_from');

    if (time_to) params.set('time_to', time_to);
    else params.delete('time_to');

    if (type) params.set('type', type);
    else params.delete('type');

    window.history.pushState({}, '', url.pathname + '?' + params.toString());
    window.location.reload();
  });

  $('#find-2').on('click', function(event) {
    event.stopPropagation();

    const formatDate = (dateStr) => {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      if (isNaN(date)) return '';
      return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0');
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