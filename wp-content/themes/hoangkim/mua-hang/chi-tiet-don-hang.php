<?php
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$user_id = get_current_user_id();
if (!$id) {
    echo "<script>location.href = '" . site_url('/404') . "';</script>";
    exit();
}
$order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}orders WHERE id = %d", $id));
$order_id = $order->id ?? 0;

if ($order) {
} else {
    echo "<script>location.href = '" . site_url('/404') . "';</script>";
    exit();
}
$status_str = ["", "Chờ báo giá", 'Đang mua hàng', 'Đã mua hàng', 'NCC phát hàng', 'Nhập kho TQ', 'Nhập kho VN', 'Khách nhận hàng', 'Đơn hàng hủy', 'Đơn khiếu nại'];
$cart_ids_array = json_decode($order->cart_ids, true);
$placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders)",
    ...$cart_ids_array
);
$carts = $wpdb->get_results($query);
$exchange_rate = isset($order->exchange_rate) ? $order->exchange_rate : null;
if (!$exchange_rate) {
    $exchange_rate = floatval(get_option('exchange_rate', 1.0));
}
$isDisabled = $order->status > 1 ? 'disabled' : '';
$totalPrice = 0;
$phone = $order->phone;
if (!$phone) {
    $phone = $order->email;
}
$query = "SELECT text,is_system FROM {$wpdb->prefix}chat WHERE order_id = $order->id";
$chats = $wpdb->get_results($query);
$create_at = DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('d/m/Y H:i');
$ngay_dat_coc = trim($order->ngay_dat_coc) ? DateTime::createFromFormat('Y-m-d H:i:s', $order->ngay_dat_coc)->format('d/m/Y H:i') : "...";
$da_mua_hang =  $order->da_mua_hang ? DateTime::createFromFormat('Y-m-d H:i:s', $order->da_mua_hang)->format('d/m/Y H:i') : "...";
$ngay_nhap_kho_tq = $order->ngay_nhap_kho_tq ? DateTime::createFromFormat('Y-m-d H:i:s', $order->ngay_nhap_kho_tq)->format('d/m/Y H:i') : "...";
$ngay_nhap_kho_vn =  $order->ngay_nhap_kho_vn ? DateTime::createFromFormat('Y-m-d H:i:s', $order->ngay_nhap_kho_vn)->format('d/m/Y H:i') : "...";
$ngay_nhan_hang = $order->ngay_nhan_hang ? DateTime::createFromFormat('Y-m-d H:i:s', $order->ngay_nhan_hang)->format('d/m/Y H:i') : "...";
$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}history_orders_transaction WHERE order_id = %s", $order_id);
$history_transactions = $wpdb->get_results($query);
$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}packages WHERE order_id = %d", $order_id);
$packages = $wpdb->get_results($query);
$total_kg_tinh_phi = array_reduce($packages, function ($carry, $package) {
    return $carry + ($package->can_nang ?? 0);
}, 0);
?>

<div class="dashboard chi-tiet-don-hang">
  <div class="mt-3 flex-1">
    <div class="d-flex align-items: center gap-3 mb-2">
      <h4 class="text-uppercase mb-0">Chi tiết đơn hàng</h4>
      <div class="status-box <?php echo ($order->status == 8 ? "box-red" : "") ?>">
        <?php echo $status_str[$order->status] ?>
      </div>
    </div>
    <div class="notification-dashboard">
      <div class="d-flex gap-2">Mã: <h5>
          MS<?php echo str_pad($user_id, 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($order->id, 2, '0', STR_PAD_LEFT); ?>
        </h5>
      </div>
      <div class="list-status order-status">
        <?php foreach ($status_str as $key => $status) { ?>
        <?php if ($key > 0) { ?>
        <div
          class="<?php echo (($order->status == 8 && $key === intval($order->status)) ? "status-red" : "") ?> <?php echo ($key === intval($order->status) ? "status-active" : "") ?>"
          data-item="<?php echo $key ?>">
          <?php echo $status ?>
        </div>
        <?php if ($key !== count($status_str) - 1) { ?>
        <i class="fa-solid fa-chevron-right"></i>
        <?php } ?>
        <?php } ?>
        <?php } ?>
      </div>

      <div class="content-list-data gap-4">
        <div class="flex-1">
          <div class="accordion mt-3" id="accordionPanelsStayOpenExample">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button text-uppercase p-3 fw-bold" type="button" data-bs-toggle="collapse"
                  data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false"
                  aria-controls="panelsStayOpen-collapseOne">
                  <span class="fa fa-cube" style="margin-right:8px;"></span> Danh sách kiện hàng
                  (<?php echo count($packages); ?>)
                </button>
              </h2>
              <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <div class="table-responsive">
                    <table class="w-100 mt-2 table-list-order">
                      <thead>
                        <tr>
                          <th>STT</th>
                          <th>Mã kiện</th>
                          <th>Cân nặng</th>
                          <th>Thể tích</th>
                          <th>Trạng thái</th>
                          <th>Thời gian</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($packages)) { ?>
                        <tr>
                          <td colspan="7" class="text-center">Không có dữ liệu</td>
                        </tr>
                        <?php } else { ?>
                        <?php foreach ($packages as $index => $package) { ?>
                        <tr>
                          <td><?php echo $index + 1; ?></td>
                          <td><?php echo $package->ma_kien; ?></td>
                          <td><?php echo format_weight($package->can_nang?? 0); ?></td>
                          <td><?php echo format_khoi($package->the_tich?? 0); ?></td>
                          <td><?php echo $package->trang_thai_kien; ?></td>
                          <td>
                            <?php echo DateTime::createFromFormat('Y-m-d H:i:s', $package->created_at)->format('d/m/Y H:i'); ?>
                          </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                        <tr>
                          <td colspan="3">Tổng kg tính phí:
                            <?php echo format_weight($total_kg_tinh_phi) ?>
                          </td>
                          <td colspan="3">Tổng tiền vận chuyển (tính riêng khi xuất hàng):
                            <?php echo format_price_vnd($order->tien_van_chuyen ?? 0) ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed text-uppercase p-3 fw-bold" type="button"
                  data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                  aria-controls="panelsStayOpen-collapseTwo">
                  <span class="fa fa-truck" style="margin-right:8px;"></span> Hành trình đơn hàng
                </button>
              </h2>
              <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <div class="d-flex">
                    <div class="history-all">
                      <div class="history-content clearfix">
                        <ul>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Tạo ngày:</span>
                            <p><?php echo $create_at ?></p>
                          </li>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Đã đặt cọc:</span>
                            <p><?php echo $ngay_dat_coc ?></p>
                          </li>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Đã mua hàng:</span>
                            <p><?php echo $da_mua_hang ?></p>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="history-all">
                      <div class="history-content clearfix">
                        <ul>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Kho Trung Quốc:</span>
                            <p><?php echo $ngay_nhap_kho_tq ?></p>
                          </li>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Kho Việt Nam:</span>
                            <p><?php echo $ngay_nhap_kho_vn ?></p>
                          </li>
                          <li class="ng-scope d-flex gap-3">
                            <span class="text-transform ng-binding fw-bold">Khách nhận hàng:</span>
                            <p><?php echo $ngay_nhan_hang ?></p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed text-uppercase p-3 fw-bold" type="button"
                  data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                  aria-controls="panelsStayOpen-collapseThree">
                  <span class="fa fa-dollar" style="margin-right:8px;"></span> Lịch sử giao dịch
                </button>
              </h2>
              <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <div class="table-responsive">
                    <table class="w-100 mt-2 table-list-order">
                      <thead>
                        <tr>
                          <th>Ngày thanh toán</th>
                          <th>Loại thanh toán</th>
                          <th>Hình thức thanh toán</th>
                          <th>Số tiền (VNĐ)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($history_transactions)) { ?>
                        <tr>
                          <td colspan="4" class="text-center">Không có dữ liệu</td>
                        </tr>
                        <?php } else { ?>
                        <?php foreach ($history_transactions as $transaction) { ?>
                        <tr>
                          <td>
                            <?php echo DateTime::createFromFormat('Y-m-d H:i:s', $transaction->created_at)->format('d/m/Y H:i'); ?>
                          </td>
                          <td><?php echo $transaction->loai; ?></td>
                          <td><?php echo $transaction->hinh_thuc; ?></td>
                          <td><?php echo format_price_vnd($transaction->so_tien); ?></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="w-100 mt-4 table-list-order" style="min-width: 600px;">
              <thead>
                <tr>
                  <th>Sản phẩm</th>
                  <th>Số lượng</th>
                  <th>Đơn giá</th>
                  <th>Tiền hàng</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($carts as $cart) {
                  $totalPrice += ($cart->price * $cart->quantity);

                  $percent = 0;
                  if ($totalPrice < 5000000) {
                    $percent = 3;
                  } elseif ($totalPrice >= 5000000 && $totalPrice <= 50000000) {
                    $percent = 2;
                  } else {
                    $percent = 1.5; // 1.5%
                  }
                ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img width="40px" src="<?php echo $cart->product_image ?>" />
                      <div>
                        <a href="<?php echo $cart->product_url ?>">
                          <?php
                            $url_without_https = str_replace("https://", "", $cart->product_url);
                            $parts = explode("/", $url_without_https);
                            echo $parts[0];
                          ?>
                        </a>
                        <div><?php echo $cart->size ?> <br><?php echo $cart->color ?></div>
                      </div>

                    </div>
                  </td>
                  <td>
                    <input <?php echo $order->status > 1 ? "disabled" : '' ?> data-type="quantity-cart"
                      data-item="<?php echo $cart->id ?>" value="<?php echo $cart->quantity ?>" />
                  </td>
                  <td>
                    <?php echo $cart->price ?? 0 ?>¥
                  </td>
                  <td>
                    <?php echo format_price_vnd(($cart->price * $cart->quantity * $exchange_rate) ?? 0) ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php if ($order->status < 2 && $order->type !== '1') { ?>
          <div class="mt-4 d-flex gap-3">
            <button id="cancel-order-fields" class="btn btn-danger fs-13">Huỷ đơn hàng</button>
            <button id="update-order-fields" class="btn btn-primary fs-13">Lưu thay đổi</button>
          </div>
          <?php } ?>
        </div>
        <div class="col-xl-3 mt-4 fs-13">
          <div class="divider d-flex justify-content-between align-items-center">
            Tỷ giá tiền tệ:
            <strong class="badge bg-secondary">
              <?php echo format_price_vnd($exchange_rate ?? 0) ?>/¥
            </strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            (1) Tiền hàng:
            <strong><?php echo format_price_vnd($totalPrice * $exchange_rate ?? 0) ?>
              (<?php echo $totalPrice ?? 0 ?>¥)</strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            (2) Phí dịch vụ(<?php echo $percent ?>%):
            <strong><?php echo format_price_vnd($order->chiet_khau_dich_vu * $exchange_rate ?? 0) ?> </strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            (3) Phí ship nội địa TQ:
            <strong><?php echo format_price_vnd($order->phi_ship_noi_dia * $exchange_rate ?? 0) ?>
              (<?php echo $order->phi_ship_noi_dia ?? 0 ?>¥)</strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            (4) Phí kiểm đếm:
            <strong><?php echo format_price_vnd($order->phi_kiem_dem ?? 0) ?></strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            (5) Phí gia cố:
            <strong><?php echo format_price_vnd($order->phi_gia_co * $exchange_rate ?? 0) ?>
              (<?php echo $order->phi_gia_co ?? 0 ?>¥)</strong>
          </div>
          <div style="color: orange" class="divider d-flex justify-content-between align-items-center">
            Số tiền phải đặt cọc (80%):
            <strong><?php echo format_price_vnd(($totalPrice * $exchange_rate) * 0.8) ?></strong>
          </div>
          <div class="divider d-flex justify-content-between align-items-center">
            <strong>Tổng tạm tính (1+2+3+4+5):</strong>
            <strong>
              <?php
                $total = $totalPrice * $exchange_rate;
                $total += $order->phi_ship_noi_dia * $exchange_rate;
                $total += $order->phi_kiem_dem;
                $total += $order->phi_gia_co * $exchange_rate;
                $total += $order->chiet_khau_dich_vu * $exchange_rate;
                echo format_price_vnd($total);
                ?>
            </strong>
          </div>
          <div style="color: green" class="divider d-flex justify-content-between align-items-center">
            Đã thanh toán:
            <strong><?php echo format_price_vnd($order->da_thanh_toan) ?></strong>
          </div>
          <div style="color: red" class="divider d-flex justify-content-between align-items-center">
            Còn thiếu:
            <strong><?php echo format_price_vnd($total - $order->da_thanh_toan) ?></strong>
          </div>
          <div class="mt-3">
            <div class="chat-box-message">
              <h6>Trao đổi với nhân viên::</h6>
              <?php foreach ($chats as $chat) { ?>
              <div style="height: 200px; overflow: auto"
                class="<?php echo ($chat->is_system === '1' ? " admin-system" : '') ?>"><?php echo trim($chat->text) ?>
              </div>
              <?php } ?>
            </div>
            <div class="position-relative">
              <textarea class="input-chat-message" placeholder="Nhập để trao đổi"></textarea>
              <button id="btn-send-chat" class="btn btn-primary fs-13 position-absolute"
                style="right: 4px; bottom: 8px">Gửi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
<?php if (!$isDisabled) { ?>
$('#update-order-fields').on('click', function(e) {
  e.preventDefault();
  var note = $('#order-note').val();
  var is_gia_co = $('#is_gia_co').is(':checked') ? 1 : 0;
  var is_kiem_dem_hang = $('#is_kiem_dem_hang').is(':checked') ? 1 : 0;
  var is_bao_hiem = $('#is_bao_hiem').is(':checked') ? 1 : 0;
  $.ajax({
    url: '<?php echo admin_url("admin-ajax.php"); ?>',
    type: 'POST',
    data: {
      action: 'update_order_fields',
      order_id: '<?php echo $order->id ?>',
      note: note,
      is_gia_co: is_gia_co,
      is_kiem_dem_hang: is_kiem_dem_hang,
      is_bao_hiem: is_bao_hiem,
    },
    success: function(response) {
      alert(response.data.message);
      window.location.reload();
    },
    error: function() {
      alert('Có lỗi xảy ra trong quá trình gửi yêu cầu.');
    }
  });
});
$('input[data-type="quantity-cart"]').on("change", function() {
  const quantity = $(this).val()
  const cart_id = $(this).attr('data-item');
  $.ajax({
    url: '<?php echo admin_url("admin-ajax.php"); ?>',
    type: 'POST',
    data: {
      action: 'update_cart_quantity',
      quantity,
      cart_id,
      order_id: '<?php echo $order->id ?>'
    },
    success: function(response) {
      window.location.reload();
    },
    error: function() {
      alert('Có lỗi xảy ra trong quá trình gửi yêu cầu.');
    }
  });
})
$('#cancel-order-fields').on("click", function() {
  const bool = confirm("Bạn có chắc chắn muốn huỷ đơn hàng này không?")
  if (bool) {
    $.ajax({
      url: '<?php echo admin_url("admin-ajax.php"); ?>',
      type: 'POST',
      data: {
        action: 'cancel_order',
        order_id: '<?php echo $order->id ?>'
      },
      success: function(response) {
        alert(response.data.message);
        window.location.reload();
      },
      error: function() {
        alert('Có lỗi xảy ra trong quá trình gửi yêu cầu.');
      }
    });
  }
})
<?php } ?>
$('#btn-send-chat').on("click", function() {
  const text = $('.input-chat-message').val();
  if (!text.trim()) return
  $('.input-chat-message').val("");
  $.ajax({
    url: '<?php echo admin_url("admin-ajax.php"); ?>',
    type: 'POST',
    data: {
      action: 'send_chat',
      order_id: '<?php echo $order->id ?>',
      text: text.trim()
    },
    success: function(response) {
      window.location.reload();
    },
    error: function() {
      alert('Có lỗi xảy ra trong quá trình gửi yêu cầu.');
    }
  });
})
</script>