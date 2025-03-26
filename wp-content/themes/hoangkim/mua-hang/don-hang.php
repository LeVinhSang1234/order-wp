<?php
global $wpdb;
$table_name = $wpdb->prefix . 'orders';
$user_id = get_current_user_id();
$time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$status_search = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$order_id = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : '';
$status_values = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
$totals = [];
foreach ($status_values as $status) {
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->prefix}orders WHERE status = %d AND user_id = %d AND type = 0", $status, $user_id));
    $totals[$status] = is_null($count) ? 0 : $count;
}

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

if (!empty($status_search)) {
    $query .= " AND status = %s";
    $params[] = $status_search;
}

if (!empty($order_id)) {
    $newString = str_replace("HK_", "", $order_id);
    $query .= " AND id LIKE %s";
    $params[] = '%' . $wpdb->esc_like($newString) . '%';
}

$query .= " AND type = 0 ORDER BY created_at ASC, created_at DESC ";

$orders = $wpdb->get_results($wpdb->prepare($query, ...$params));
$exchange_rate = floatval(get_option('exchange_rate', 1.0));

$status_str = ["", "Chờ báo giá", 'Đang mua hàng', 'Đã mua hàng', 'NCC phát hàng', 'Nhập kho TQ', 'Nhập kho VN', 'Khách nhận hàng', 'Đơn hàng hủy', 'Đơn khiếu nại'];
?>

<div class="dashboard">
    <div class="mt-3 text-uppercase flex-1">
        <h4>Danh sách đơn hàng</h4>
        <div class="notification-dashboard">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input id="order_id" class="w-filter-full" placeholder="Mã đơn hàng" />
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
                <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="status-tabs d-flex flex-wrap gap-2 mt-2">
                <button class="status-tab <?php echo empty($status_search) ? 'active' : ''; ?>" data-status="">Tất cả</button>
                <button class="status-tab <?php echo $status_search == '1' ? 'active' : ''; ?>" data-status="1">Chờ báo giá (<?php echo $totals[1] ?>)</button>
                <button class="status-tab <?php echo $status_search == '2' ? 'active' : ''; ?>" data-status="2">Đang mua hàng (<?php echo $totals[2] ?>)</button>
                <button class="status-tab <?php echo $status_search == '3' ? 'active' : ''; ?>" data-status="3">Đã mua hàng (<?php echo $totals[3] ?>)</button>
                <button class="status-tab <?php echo $status_search == '4' ? 'active' : ''; ?>" data-status="4">NCC phát hàng (<?php echo $totals[4] ?>)</button>
                <button class="status-tab <?php echo $status_search == '5' ? 'active' : ''; ?>" data-status="5">Nhập kho TQ (<?php echo $totals[5] ?>)</button>
                <button class="status-tab <?php echo $status_search == '6' ? 'active' : ''; ?>" data-status="6">Nhập kho VN (<?php echo $totals[6] ?>)</button>
                <button class="status-tab <?php echo $status_search == '7' ? 'active' : ''; ?>" data-status="7">Khách nhận hàng (<?php echo $totals[7] ?>)</button>
                <button class="status-tab <?php echo $status_search == '8' ? 'active bg-danger btn-danger' : ''; ?>" data-status="8">Đơn hàng hủy (<?php echo $totals[8] ?>)</button>
                <button class="status-tab <?php echo $status_search == '9' ? 'active bg-warning btn-warning text-secondary' : ''; ?>" data-status="9">Đơn khiếu nại (<?php echo $totals[9] ?>)</button>
            </div>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>Số đơn hàng: <strong><?php echo str_pad(count($orders), 2, "0", STR_PAD_LEFT); ?></strong></div>
                    <button class="btn btn-primary" id="dat-coc-toan-bo">$ Đặt cọc toàn bộ</button>
                </div>
                <div class="table-responsive">
                    <table class="w-100 mt-2 table-list-order" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" id="check-all-order">
                                </th>
                                <th class="text-center">STT</th>
                                <th>Mã đơn hàng</th>
                                <th class="text-center">Sản phẩm</th>
                                <th>Tổng Tiền (VNĐ)</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Thao Tác</th>
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
                                        "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders) limit 1",
                                        ...$cart_ids_array
                                    );
                                    $carts = $wpdb->get_results($query);
                                } else {
                                    $carts = [];
                                }
                                $image_url = isset($carts[0]->product_image) ? $carts[0]->product_image : '';
                                if (!$image_url) {
                                    $image_url = $order->link_hinh_anh;
                                }
                                $total = 0;
                                foreach ($carts as $cart) {
                                    $total += floatval($cart->price) * intval($cart->quantity);
                                }
                                $total = $total * $exchange_rate;
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at);
                            ?>
                                <tr style="text-transform: initial">
                                    <td class="text-center">
                                    <?php if (intval($order->status) !== 8 && intval($order->status) !== 2) { ?>
                                        <input type="checkbox">
                                    <?php } ?>
                                    </td>
                                    <td class="text-center"><?php echo $stt++; ?></td>
                                    <td>MS<?php echo str_pad($user_id, 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($order->id, 2, '0', STR_PAD_LEFT); ?></td>
                                    <td class="text-center">
                                        <img src="<?php echo $image_url ?>" />
                                    </td>
                                    <td style="font-size: 12px">
                                        <div class="d-flex justify-content-between">Tổng tiền hàng:<strong><?php echo format_price_vnd($total) ?></strong></div>
                                        <div class="d-flex justify-content-between">Tiền phải cọc:<span style="color: orange"><?php echo format_price_vnd($total  * 0.8) ?></span></div>
                                        <div class="d-flex justify-content-between">Tiền thanh toán:<span style="color: green"><?php echo format_price_vnd($order->da_thanh_toan) ?></span></div>
                                        <div class="d-flex justify-content-between">Tiền hàng còn thiếu:<span style="color: #ff0000"><?php echo format_price_vnd($total - $order->da_thanh_toan) ?></span></div>
                                        <div class="d-flex justify-content-between">Tổng hoàn:<span>0 đ</span></div>
                                    </td>
                                    <td>
                                        Tạo ngày <?php echo $date->format('d/m/Y H:i') ?>
                                    </td>
                                    <td style="color: <?php 
                                        echo ($order->status === '8') ? "#ff0000" : 
                                             (($order->status === '9') ? "#ffc107" : "green"); 
                                    ?>; font-weight: 600">
                                        <?php echo isset($status_str[$order->status]) ? $status_str[$order->status] : $status_str[1] ?>
                                    </td>
                                    <td>
                                        <div><a href="<?php echo site_url() . '/chi-tiet-don-hang?id=' . $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-primary mb-2">Chi tiết</a></div>
                                        <?php if ($order->status !== '8') { ?>
                                            <div><button button-type="khieu-nai" data-item="<?php echo $order->id ?>" style="min-width: 120px; font-size: 13px" class="btn btn-danger mb-2">Khiếu nại</button></div>
                                        <?php } ?>
                                        <?php if (intval($order->status) == 8) { ?>
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

<style>
.status-tabs {
    margin: 10px 0;
    width: 100%;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}
.status-tab {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 5px;
    padding: 4px 16px;
}
.status-tab:hover {
    background: #f5f5f5;
}
.status-tab.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}
</style>

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

    $(document).ready(function() {
        const params = new URLSearchParams(window.location.search);
        if (params.has('order_id')) $('#order_id').val(params.get('order_id'));
        if (params.has('time_from')) $('#time_from').val(params.get('time_from').replace(/\//g, '-'));
        if (params.has('time_to')) $('#time_to').val(params.get('time_to').replace(/\//g, '-'));
        if (params.has('status')) {
            $('.status-tab[data-status="' + params.get('status') + '"]').addClass('active');
        }

        $('.status-tab').on('click', function() {
            $('.status-tab').removeClass('active');
            $(this).addClass('active');

            // Trigger search automatically when tab is clicked
            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                if (isNaN(date)) return '';
                return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0');
            };

            const time_from = formatDate($('#time_from').val());
            const time_to = formatDate($('#time_to').val());
            const status = $(this).data('status');
            const order_id = $('#order_id').val();
            let url = new URL(window.location.href);
            let params = url.searchParams;

            if (time_from) params.set('time_from', time_from);
            else params.delete('time_from');

            if (time_to) params.set('time_to', time_to);
            else params.delete('time_to');

            if (status) params.set('status', status);
            else params.delete('status');

            if (order_id) params.set('order_id', order_id);
            else params.delete('order_id');

            window.history.pushState({}, '', url.pathname + '?' + params.toString());
            window.location.reload();
        });

        $('.btn-find').on('click', function(event) {
            event.stopPropagation();

            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                if (isNaN(date)) return '';
                return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0');
            };

            const time_from = formatDate($('#time_from').val());
            const time_to = formatDate($('#time_to').val());
            const status = $('.status-tab.active').data('status');
            const order_id = $('#order_id').val();
            let url = new URL(window.location.href);
            let params = url.searchParams;

            if (time_from) params.set('time_from', time_from);
            else params.delete('time_from');

            if (time_to) params.set('time_to', time_to);
            else params.delete('time_to');

            if (status) params.set('status', status);
            else params.delete('status');

            if (order_id) params.set('order_id', order_id);
            else params.delete('order_id');

            window.history.pushState({}, '', url.pathname + '?' + params.toString());
            window.location.reload();
        });

        $('#check-all-order').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('tbody input[type="checkbox"]').prop('checked', isChecked);
        });

        $('#dat-coc-toan-bo').on('click', function() {
            const selectedOrders = $('tbody input[type="checkbox"]:checked');
            if (selectedOrders.length === 0) {
                alert('Hãy chọn đơn hàng để đặt cọc.');
                return;
            }

            let totalDeposit = 0;
            let orderList = [];
            let orderIds = [];
            selectedOrders.each(function() {
                const row = $(this).closest('tr');
                const depositAmount = parseFloat(row.find('td:nth-child(5) span[style="color: orange"]').text().replace(/[^\d]/g, '')) || 0;
                totalDeposit += depositAmount;

                const orderId = row.find('td:nth-child(3)').text().trim(); // Get order ID in the desired format
                orderList.push(orderId);
                orderIds.push(orderId.replace(/^MS\d+-/, '')); // Extract numeric order ID
            });

            const orderListString = orderList.join(', ');
            if (confirm(`Hãy chắc chắn đặt cọc. Tổng số tiền đặt cọc là: ${totalDeposit.toLocaleString()} VNĐ\nDanh sách đơn hàng: ${orderListString}`)) {
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'update_order_status',
                        status: 2,
                        order_ids: orderIds
                    },
                    success: function(response) {
                        alert(response.data.message);
                        window.location.reload();
                    },
                    error: function() {
                        alert('Lỗi kết nối đến máy chủ.');
                    }
                });
            }
        });

    });
</script>
