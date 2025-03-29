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

$query .= " AND status = 9 ORDER BY created_at DESC";

$orders = $wpdb->get_results($wpdb->prepare($query, ...$params));
$status_str = ["", "Chờ báo giá", 'Đang mua hàng', 'Đã mua hàng', 'NCC phát hàng', 'Nhập kho TQ', 'Nhập kho VN', 'Khách nhận hàng', 'Đơn hàng hủy', 'Đơn khiếu nại'];
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
                <table class="w-100 mt-2 table-list-order" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th style="width: 100px;" class="text-center">STT</th>
                                <th style="width: 140px;">Mã đơn hàng</th>
                                <th class="text-center">Sản phẩm</th>
                                <th>Tổng Tiền (VNĐ)</th>
                                <th style="width: 140px;">Trạng thái</th>
                                <!-- <th class="text-center" style="width: 130px;">Thao Tác</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stt = 1;
                            foreach ($orders as $order) {
                                $cart_ids_array = !empty($order->cart_ids) ? json_decode($order->cart_ids, true) : [];
                                if (!is_array($cart_ids_array)) {
                                    $cart_ids_array = [];
                                }
                                if (!empty($cart_ids_array)) {
                                    $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
                                    $query = $wpdb->prepare(
                                        "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders)",
                                        ...$cart_ids_array
                                    );
                                    $carts = $wpdb->get_results($query);
                                } else {
                                    $carts = [];
                                }
                                $image_urls = [];
                                foreach ($carts as $cart) {
                                    if (!empty($cart->product_image)) {
                                        $image_urls[] = $cart->product_image; // Thêm liên kết hình ảnh vào mảng
                                    }
                                }
                                if (empty($image_urls)) {
                                    $image_urls[] = $order->link_hinh_anh;
                                }
                                $exchange_rate = isset($order->exchange_rate) ? $order->exchange_rate : null;
                                if (!$exchange_rate) {
                                    $exchange_rate = floatval(get_option('exchange_rate', 1.0));
                                }
                                $total = 0;
                                foreach ($carts as $cart) {
                                    $total += floatval($cart->price) * $exchange_rate  * intval($cart->quantity);
                                }
                                $total = $total;
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at);
                            ?>
                                <tr style="text-transform: initial">
                                    <td class="text-center"><?php echo $stt++; ?></td>
                                    <td>MS<?php echo str_pad($user_id, 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($order->id, 2, '0', STR_PAD_LEFT); ?></td>
                                    <td class="text-center flex-wrap">
                                        <?php foreach ($image_urls as $image_url) {
                                            echo '<img style="margin-right: 8px;" src="' . esc_url($image_url) . '" alt="Product Image" />';
                                        } ?>
                                    </td>
                                    <td style="font-size: 12px">
                                        <div class="d-flex justify-content-between">Tạo ngày:<strong><?php echo $date->format('d/m/Y H:i') ?></strong></div>
                                        <div class="d-flex justify-content-between">Tổng tiền hàng:<strong><?php echo format_price_vnd($total) ?></strong></div>
                                        <div class="d-flex justify-content-between" data-coc="<?php echo $total * 0.8; ?>">Tiền phải cọc:<span style="color: orange"><?php echo format_price_vnd($total * 0.8) ?></span></div>
                                        <div class="d-flex justify-content-between">Tiền thanh toán:<span style="color: green"><?php echo format_price_vnd($order->da_thanh_toan) ?></span></div>
                                        <div class="d-flex justify-content-between">Tiền hàng còn thiếu:<span style="color: #ff0000"><?php echo format_price_vnd($total - $order->da_thanh_toan) ?></span></div>
                                        <div class="d-flex justify-content-between">Tổng hoàn:<span><?php echo format_price_vnd($order->da_hoan) ?></span></div>
                                    </td>
                                    <td style="color: <?php
                                                        echo ($order->status === '8') ? "#ff0000" : (($order->status === '9') ? "#ffc107" : "green");
                                                        ?>; font-weight: 600">
                                        <?php echo isset($status_str[$order->status]) ? $status_str[$order->status] : $status_str[1] ?>
                                    </td>
                                    <!-- <td class="text-center">
                                        <div><a href="<?php echo site_url() . '/chi-tiet-don-hang?id=' . $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-primary mb-2">Chi tiết</a></div>
                                        <?php if ($order->status !== '8') { ?>
                                            <div><button button-type="khieu-nai" data-item="<?php echo $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-danger mb-2">Khiếu nại</button></div>
                                        <?php } ?>
                                        <button id="btn-dat-lai-don" data-item="<?php echo $order->id ?>" style="min-width: 120px; font-size: 13px; background-color: #28b779" class="btn">Đặt lại đơn</button>
                                    </td> -->
                                </tr>
                            <?php } ?>
                        </tbody>
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