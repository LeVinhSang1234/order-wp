<?php
global $wpdb;
$user_id = get_current_user_id();
$time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$van_don = isset($_GET['van_don']) ? sanitize_text_field($_GET['van_don']) : '';

$query = "SELECT * FROM {$wpdb->prefix}orders WHERE user_id = %d";
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
    $query .= " AND status = %s";
    $params[] = $status;
}

if (!empty($van_don)) {
    $newString = str_replace("HK_", "", $van_don);
    $query .= " AND id LIKE %s";
    $params[] = '%' . $wpdb->esc_like($newString) . '%';
}

$query .= " AND status = 10 ORDER BY created_at DESC";

$orders = $wpdb->get_results($wpdb->prepare($query, ...$params));
$status_str = ["", "Chờ đặt cọc", 'Chờ mua hàng', 'Đang mua hàng', 'Chờ shop phát hàng', 'Shop TQ Phát hàng', 'Kho TQ nhận hàng', 'Xuất kho TQ', 'Trong kho VN', 'Sẵn sàng giao hàng', 'Chờ xử lý khiếu nại', 'Đã kết thúc', 'Đã hủy'];
$exchange_rate = floatval(get_option('exchange_rate', 1.0));
$phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));
?>

<div class="dashboard">
    <div class="mt-3 text-uppercase flex-1">
        <h4>DANH SÁCH KHIẾU NẠI SHOP</h4>
        <div class="notification-dashboard">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input id="van_don" class="w-filter-full" placeholder="Mã đơn hàng" />
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
                <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="mt-3">
                Số khiếu nại: <strong><?php echo count($orders) ?></strong>
                <div class="table-responsive">
                    <table class="w-100 mt-2" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th style="width: 70px" class="text-center">#</th>
                                <th>Thông tin đơn hàng</th>
                                <th>Thông tin tài chính</th>
                                <th>Trạng thái đơn hàng</th>
                            </tr>
                        </thead>
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
                                <td style="font-size: 12px">
                                    <div class="d-flex justify-content-between">Tổng tiền
                                        hàng:<strong><?php echo format_price_vnd($total) ?></strong></div>
                                    <div class="d-flex justify-content-between">Tiền thanh toán:<span
                                            style="color: green"><?php echo format_price_vnd($order->da_thanh_toan) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">Tiền hàng còn thiếu:<span
                                            style="color: #ff0000"><?php echo format_price_vnd($total - $order->da_thanh_toan) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">Tổng hoàn:<span>0 đ</span></div>
                                </td>
                                <td
                                    style="color: <?php echo ($order->status === '10' ? "#ff0000" : "green") ?>; font-weight: 600">
                                    <?php echo isset($status_str[$order->status]) ? $status_str[$order->status] : $status_str[1] ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const params = new URLSearchParams(window.location.search);

        if (params.has('time_from')) $('#time_from').val(params.get('time_from').replace(/\//g, '-'));
        if (params.has('time_to')) $('#time_to').val(params.get('time_to').replace(/\//g, '-'));
        if (params.has('van_don')) $('#van_don').val(params.get('van_don'));

        $('.btn-find').on('click', function (event) {
            event.stopPropagation();

            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                if (isNaN(date)) return '';
                return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0');
            };

            const time_from = formatDate($('#time_from').val());
            const time_to = formatDate($('#time_to').val());
            const status = $('#status').val();
            const van_don = $('#van_don').val();
            let url = new URL(window.location.href);
            let params = url.searchParams;

            if (time_from) params.set('time_from', time_from);
            else params.delete('time_from');

            if (time_to) params.set('time_to', time_to);
            else params.delete('time_to');

            if (status) params.set('status', status);
            else params.delete('status');

            if (van_don) params.set('van_don', van_don);
            else params.delete('van_don');

            window.history.pushState({}, '', url.pathname + '?' + params.toString());
            window.location.reload();
        });
    })


</script>