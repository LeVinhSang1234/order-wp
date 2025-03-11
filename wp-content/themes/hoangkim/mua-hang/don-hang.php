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
                    <option>Chờ đặt cọc (<?php echo $totals[1] ?>)</option>
                    <option>Chờ mua hàng (<?php echo $totals[2] ?>)</option>
                    <option>Đang mua hàng (<?php echo $totals[3] ?>)</option>
                    <option>Chờ shop phát hàng (<?php echo $totals[4] ?>)</option>
                    <option>Shop TQ Phát hàng (<?php echo $totals[5] ?>)</option>
                    <option>Kho TQ nhận hàng (<?php echo $totals[6] ?>)</option>
                    <option>Xuất kho TQ (<?php echo $totals[7] ?>)</option>
                    <option>Trong kho VN (<?php echo $totals[8] ?>)</option>
                    <option>Sẵn sàng giao hàng (<?php echo $totals[9] ?>)</option>
                    <option>Chờ xử lý khiếu nại (<?php echo $totals[10] ?>)</option>
                    <option>Đã kết thúc (<?php echo $totals[11] ?>)</option>
                    <option>Đã hủy (<?php echo $totals[12] ?>)</option>
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
                <table class="w-100 mt-2">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã đơn hàng</th>
                            <th>Sản phẩm</th>
                            <th>Tổng Tiền (VNĐ)</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) { ?>
                            <tr>
                                <td><?php echo $order->id ?></td>
                                <td><?php echo "HK_" . $order->id ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>