<?php
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$user_id = get_current_user_id();
if (!$id) {
    echo "<script>location.href = '" . site_url('/404') . "';</script>";
    exit();
}
$order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}orders WHERE id = %d", $id));
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
            <div class="d-flex gap-2">Mã: <h5>MS<?php echo str_pad($user_id, 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($order->id, 2, '0', STR_PAD_LEFT); ?></h5>
            </div>
            <div class="list-status order-status">
                <?php foreach ($status_str as $key => $status) { ?>
                    <?php if ($key > 0) { ?>
                        <div class="<?php echo (($order->status == 8 && $key === intval($order->status)) ? "status-red" : "") ?> <?php echo ($key === intval($order->status) ? "status-active" : "") ?>" data-item="<?php echo $key ?>">
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
                    <div class="d-flex mt-4 fs-14 ">
                        <div class="flex-1">
                            <strong>
                                <i class="fa-solid fa-location-dot"></i>
                                Địa chỉ nhận hàng
                            </strong>
                            <div class="mt-2 fs-13">Người nhận: <strong><?php echo $order->ho_ten; ?></strong> / <?php echo $phone; ?></div>
                            <div class="mt-1 fs-13"><?php echo $order->address; ?></div>
                            <textarea <?php echo $order->status > 1 ? "disabled" : '' ?> id="order-note" class="mt-1 fs-13" placeholder="Ghi chú đơn hàng"><?php echo $order->note; ?></textarea>
                        </div>
                        <div class="flex-1">
                            <strong>
                                <i class="fa-solid fa-location-dot"></i>
                                Dịch vụ sử dụng
                            </strong>
                            <ul class="ls-inline">
                                <li>
                                    <input <?php echo $isDisabled ?> <?php echo ($order->is_kiem_dem_hang ? 'checked' : '') ?> type="checkbox" id="is_kiem_dem_hang" data-orderid="16820"> Kiểm hàng
                                </li>
                                <li>
                                    <input <?php echo $isDisabled ?> <?php echo ($order->is_gia_co ? 'checked' : '') ?> type="checkbox" id="is_gia_co" data-orderid="16820"> Đóng kiện gỗ
                                </li>
                                <li>
                                    <input <?php echo $isDisabled ?> <?php echo ($order->is_bao_hiem ? 'checked' : '') ?> type="checkbox" id="is_bao_hiem" data-orderid="16820"> Bảo hiểm hàng hóa
                                </li>
                            </ul>
                        </div>
                    </div>
                    <table class="w-100 mt-4 table-list-chi-tiet">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center" style="width: 250px">Size - Màu sắc</th>
                                <th>Cửa hàng</th>
                                <th>Số lượng</th>
                                <th style="width: 120px;">Giá tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carts as $cart) {
                                $totalPrice += ($cart->price * $cart->quantity);
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img width="40px" src="<?php echo $cart->product_image ?>" />
                                            <a href="<?php echo $cart->product_url ?>">
                                                <?php
                                                $url_without_https = str_replace("https://", "", $cart->product_url);
                                                $parts = explode("/", $url_without_https);
                                                echo $parts[0];
                                                ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $cart->size ?> <br><?php echo$cart->color ?></td>
                                    <td>
                                        <a href="<?php echo $cart->shop_url ?>"><?php echo $cart->shop_id ?></a>
                                    </td>
                                    <td>
                                        <input <?php echo $order->status > 1 ? "disabled" : '' ?> data-type="quantity-cart" data-item="<?php echo $cart->id ?>" value="<?php echo $cart->quantity ?>" />
                                    </td>
                                    <td>
                                        <?php echo format_price_vnd($exchange_rate * $cart->price) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 mt-4 fs-13">
                    <div class="divider d-flex justify-content-between align-items-center">
                        Tỷ giá tiền tệ:
                        <strong><?php echo format_price_vnd($exchange_rate ?? 0) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        (1) Tiền hàng:
                        <strong><?php echo format_price_vnd($totalPrice * $exchange_rate ?? 0) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        (2) Phí ship nội địa TQ:
                        <strong><?php echo format_price_vnd($order->phi_ship_noi_dia ?? 0) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        (3) Phí kiểm đếm:
                        <strong><?php echo format_price_vnd($order->phi_kiem_dem ?? 0) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        (4) Phí gia cố:
                        <strong><?php echo format_price_vnd($order->phi_gia_co ?? 0) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        (5) Phí dịch vụ:
                        <strong><?php echo format_price_vnd($order->chiet_khau_dich_vu * $exchange_rate ?? 0) ?></strong>
                    </div>
                    <div style="color: orange" class="divider d-flex justify-content-between align-items-center">
                        Số tiền phải đặt cọc (80%):
                        <strong><?php echo format_price_vnd(($totalPrice * $exchange_rate) * 0.8) ?></strong>
                    </div>
                    <div class="divider d-flex justify-content-between align-items-center">
                        <strong>Tổng tạm tính:</strong>
                        <strong>
                            <?php
                            $total = $totalPrice * $exchange_rate;
                            $total += $order->phi_ship_noi_dia;
                            $total += $order->phi_kiem_dem;
                            $total += $order->phi_gia_co;
                            $total += $order->chiet_khau_dich_vu;
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
                    <?php if ($order->status < 2 && $order->type !== '1') { ?>
                        <div class="mt-4 d-flex gap-3">
                            <button id="cancel-order-fields" class="btn btn-danger fs-13">Huỷ đơn hàng</button>
                            <button id="update-order-fields" class="btn btn-primary fs-13">Lưu thay đổi</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="mt-3">
                <h6>Chat với chúng tôi</h6>
                <div class="chat-box-message">
                    <?php foreach ($chats as $chat) { ?>
                        <div class="<?php echo ($chat->is_system === '1' ? " admin-system" : '') ?>"><?php echo trim($chat->text) ?></div>
                    <?php } ?>
                </div>
                <textarea class="input-chat-message" placeholder="Nhập để trao đổi"></textarea>
                <button id="btn-send-chat" class="btn btn-primary fs-13">Gửi</button>
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
