<?php
$id = isset($_GET['id']) ? $_GET['id'] : 0;
if (!$id) {
    echo "<script>location.href = '" . site_url('/404') . "';</script>";
    exit();
}
$status_str = ["Chờ đặt cọc", 'Chờ mua hàng', 'Kho TQ nhận hàng', 'Xuất kho TQ', 'Trong kho VN', 'Sẵn sàng giao hàng', 'Chờ xử lý khiếu nại', 'Đã kết thúc', 'Đã hủy'];
$order = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_orders WHERE id = %d", $id));
if ($order) {
} else {
    echo "<script>location.href = '" . site_url('/404') . "';</script>";
    exit();
}
?>

<div class="dashboard chi-tiet-don-hang">
    <div class="mt-3 flex-1">
        <div class="d-flex align-items: center gap-3 mb-2">
            <h4 class="text-uppercase mb-0">Chi tiết đơn hàng</h4>
            <div class="status-box">
                <?php echo $status_str[$order->status] ?>
            </div>
        </div>
        <div class="notification-dashboard">
            <strong>Thông tin đơn hàng</strong>
            <div class="list-status">
                <?php foreach ($status_str as $key => $status) { ?>
                    <div data-item="<?php echo $key ?>"><?php echo $status ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>