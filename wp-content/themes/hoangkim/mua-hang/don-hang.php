<?php
$table_name = $wpdb->prefix . 'orders';
$user_id = get_current_user_id();
$status_values = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
$totals = [];
foreach ($status_values as $status) {
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->prefix}orders WHERE status = %d AND user_id = %d", $status, $user_id));
    $totals[$status] = is_null($count) ? 0 : $count;
}
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}orders WHERE user_id = %d ORDER BY created_at DESC",
    $user_id
);
$orders = $wpdb->get_results($query);
$exchange_rate = floatval(get_option('exchange_rate', 1.0));
$phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));

$status_str = ["", "Chờ đặt cọc", 'Chờ mua hàng', 'Đang mua hàng', 'Chờ shop phát hàng', 'Shop TQ Phát hàng', 'Kho TQ nhận hàng', 'Xuất kho TQ', 'Trong kho VN', 'Sẵn sàng giao hàng', 'Chờ xử lý khiếu nại', 'Đã kết thúc', 'Đã hủy'];
?>

<div class="dashboard">
    <div class="mt-3 text-uppercase flex-1">
        <h4>Danh sách đơn hàng</h4>
        <div class="notification-dashboard">
            <div class="d-flex align-items-center gap-2">
                <input placeholder="Mã đơn hàng" />
                <?php
                $id = "time_from";
                $placeholder = "Từ ngày";
                include get_template_directory() . '/mua-hang/input-date-picker.php';
                ?>
                <?php
                $id = "time_to";
                $placeholder = "Đến ngày";
                include get_template_directory() . '/mua-hang/input-date-picker.php';
                ?>
                <select name="status">
                    <option>Trạng thái</option>
                    <option value="1">Chờ đặt cọc (<?php echo $totals[1] ?>)</option>
                    <option value="2">Chờ mua hàng (<?php echo $totals[2] ?>)</option>
                    <option value="3">Đang mua hàng (<?php echo $totals[3] ?>)</option>
                    <option value="4">Chờ shop phát hàng (<?php echo $totals[4] ?>)</option>
                    <option value="5">Shop TQ Phát hàng (<?php echo $totals[5] ?>)</option>
                    <option value="6">Kho TQ nhận hàng (<?php echo $totals[6] ?>)</option>
                    <option value="7">Xuất kho TQ (<?php echo $totals[7] ?>)</option>
                    <option value="8">Trong kho VN (<?php echo $totals[8] ?>)</option>
                    <option value="9">Sẵn sàng giao hàng (<?php echo $totals[9] ?>)</option>
                    <option value="10">Chờ xử lý khiếu nại (<?php echo $totals[10] ?>)</option>
                    <option value="11">Đã kết thúc (<?php echo $totals[11] ?>)</option>
                    <option value="12">Đã hủy (<?php echo $totals[12] ?>)</option>
                </select>
                <select name="website">
                    <option>Website</option>
                    <option>Taobao.com</option>
                    <option>1688.com</option>
                    <option>Tmall.com</option>
                </select>
                <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="mt-3">
                Số đơn hàng: <strong>0</strong>
                <table class="w-100 mt-2 table-list-order">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Mã đơn hàng</th>
                            <th class="text-center">Sản phẩm</th>
                            <th>Tổng Tiền (VNĐ)</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) {
                            $cart_ids_array = json_decode($order->cart_ids, true);
                            $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
                            $query = $wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders) limit 1",
                                ...$cart_ids_array
                            );
                            $carts = $wpdb->get_results($query);
                            $image_url = isset($carts[0]->product_image) ? $carts[0]->product_image : '';
                            $total = 0;
                            foreach ($carts as $cart) {
                                $total += $cart->price;
                            }
                            $total = $total * $exchange_rate;
                            $total += $total * $phi_mua_hang;
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at);
                        ?>
                            <tr style="text-transform: initial">
                                <td class="text-center"><?php echo $order->id ?></td>
                                <td><?php echo "HK_" . $order->id ?></td>
                                <td class="text-center">
                                    <img src="<?php echo $image_url ?>" />
                                </td>
                                <td style="font-size: 12px">
                                    <div class="d-flex justify-content-between">Tổng tiền hàng:<strong><?php echo format_price_vnd($total) ?></strong></div>
                                    <div class="d-flex justify-content-between">Tiền thanh toán:<span style="color: green"><?php echo format_price_vnd($order->da_thanh_toan) ?></span></div>
                                    <div class="d-flex justify-content-between">Tiền hàng còn thiếu:<span style="color: #ff0000"><?php echo format_price_vnd($total - $order->da_thanh_toan) ?></span></div>
                                    <div class="d-flex justify-content-between">Tổng hoàn:<span>0 đ</span></div>
                                </td>
                                <td>
                                    Tạo ngày <?php echo $date->format('d/m/Y H:i') ?>
                                </td>
                                <td style="color: <?php echo ($order->status === '10' ? "#ff0000" : "green") ?>; font-weight: 600">
                                    <?php echo isset($status_str[$order->status]) ? $status_str[$order->status] : $status_str[1] ?>
                                </td>
                                <td>
                                    <div><a href="<?php echo site_url() . '/chi-tiet-don-hang?id=' . $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-primary mb-2">Chi tiết</a></div>
                                    <?php if ($order->status !== '10') { ?>
                                        <div><button button-type="khieu-nai" data-item="<?php echo $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-danger mb-2">Khiếu nại</button></div>
                                    <?php } ?>
                                    <?php if (intval($order->status) > 10) { ?>
                                        <div><button style="min-width: 120px; font-size: 13px; background-color: #28b779" class="btn">Đặt lại đơn</button></div>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="popup-info">
    <div class="popup-content" style="margin-top: 70px">
        <h3 class="text-center">Khiếu nại</h3>
        <div class="content-data">
            <div class="mt-3 address-info">
                <textarea id="text-khieu-nai" style="min-height: 100px; resize: none" class="w-100 fs-13" placeholder="Vui lòng nhập lý do khiếu nại"></textarea>
                <div id="error-ly-do" style="display: none;" class="text-error mt-0">Vui lòng nhập lý do</div>
            </div>
        </div>
        <button id="button_send_khieu_nai" style="float: right" class="btn btn-danger mt-3">Gửi</button>
    </div>
</div>

<script>
    $('.popup-info').on('click', function(event) {
        if (document.querySelector('.popup-content')?.contains(event.target)) return
        $(this).removeClass('popup-info__active')
    })
    $('button[button-type="khieu-nai"]').on('click', function() {
        const orderId = $(this).attr('data-item')
        $('.popup-info').addClass('popup-info__active')
        $('#button_send_khieu_nai').attr('data-item', orderId)
    })
    $('#button_send_khieu_nai').on('click', function() {
        const orderId = $(this).attr('data-item')
        const text = $('#text-khieu-nai').val()
        if (!text) {
            return $('#error-ly-do').show()
        };
        $('.text-khieu-nai').val('')
        $(this).removeClass('popup-info__active')
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: {
                action: 'send_khieu_nai',
                order_id: orderId,
                text
            },
            success: function(response) {
                alert(response.data.message);
                window.location.reload()
            },
            error: function() {
                alert('Lỗi kết nối đến máy chủ.');
            }
        });
    })
</script>