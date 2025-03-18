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
?>

<div class="dashboard">
    <div class="d-flex flex-column flex-md-row w-100 gap-2">
        <div class="mt-3 flex-3 align-items-stretch">
            <h4 class="text-uppercase">Ví điện tử</h4>
            <div class="notification-dashboard" style="height: 150px">
                <div class="d-flex align-items-center justify-content-between">
                    <div>Số dư trong ví: <strong style="color: #ff0000">0</strong> VNĐ</div>
                    <a target="__blank" href="<?php echo site_url() . '/nap-tien' ?>" class="btn btn-primary">Nạp
                        tiền</a>
                </div>
                <div>Mã nạp tiền: <strong style="color: #ff0000">HK-MS<?php echo sprintf('%02d', get_current_user_id()); ?></strong>
                </div>
                <div style="font-size: 12px" class="mt-2">
                    Tổng tiền hàng đã về chờ tất toán : <strong style="color: #ff0000">0</strong> đ
                </div>
                <div style="font-size: 12px">Tổng tiền hàng chưa về : <strong style="color: #ff0000">0</strong> đ</div>
            </div>
        </div>
        <div class="mt-3 flex-1">
            <h4 class="text-uppercase">Tài chính</h4>
            <div class="notification-dashboard" style="height: 150px">
                <div class="mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền nạp : <span><strong
                            style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng chi tiêu :
                    <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền đơn
                    hàng : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tiền đang cọc :
                    <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Cần thanh toán :
                    <span><strong style="color: #ff0000">0</strong> đ</span></div>
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
    <div class="mt-3 text-uppercase flex-1">
        <h4>Lịch sử giao dịch</h4>
        <div class="notification-dashboard">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input id="ma_phieu_thu" class="w-filter-full" placeholder="Mã phiếu thu" />
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
                <!-- <select class="w-filter-full" name="status" id="status">
                    <option value="">Trạng thái</option>
                    <option value="">Đã duyệt</option>
                    <option value="CHỜ DUYỆT">Chờ duyệt</option>
                </select> -->
                <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="mt-3">
                Số đơn hàng: <strong>0</strong>
                <div class="table-responsive">
                    <table class="w-100 mt-2" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th>Mã phiếu thu</th>
                                <th>Thông tin đơn hàng</th>
                                <th>Thông tin tài chính</th>
                                <th>Trạng thái đơn hàng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wallets as $wallet) { ?>
                                <tr>
                                    <td><?php echo ($wallet->ma_phieu_thu) ?></td>
                                    <td>--</td>
                                    <td><?php echo format_price_vnd($wallet->so_tien) ?></td>
                                    <td><?php echo ($wallet-> da_xu_ly === "0" ? 'Chờ duyệt' : 'Đã duyệt') ?></td>
                                    <td>Nạp tiền vào hệ thống</td>
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
        if (params.has('status')) $('#status').val(params.get('status'));
        if (params.has('ma_phieu_thu')) $('#ma_phieu_thu').val(params.get('ma_phieu_thu'));

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
    })


</script>