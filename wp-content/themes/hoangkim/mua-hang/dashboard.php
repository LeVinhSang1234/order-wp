<?php
global $wpdb;
$user_id = get_current_user_id();
$total_orders = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}orders WHERE user_id = %d",
    $user_id
));
$total_khieu_nai = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}orders WHERE user_id = %d AND status = 10",
    $user_id
));
$total_cart = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}cart WHERE user_id = %d and is_done = 0",
    $user_id
));
$notifications = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}notification WHERE user_id = %d ORDER BY is_read ASC, created_at DESC",
        $user_id,
    )
);
$wpdb->update(
    "{$wpdb->prefix}notification",
    array('is_read' => 1),
    array('user_id' => $user_id, 'is_read' => 0),
    array('%d'),
    array('%d', '%d')
);
?>

<div class="dashboard">
    <div class="row">
        <div class="col-lg-3 col-md-6 pb-2">
            <div class="box-dashboard box-dashboard-green">
                <h4>0 đ</h4>
                <div class="title">Số dư</div>
                <a href="/vi-dien-tu" class="view-detail">Xem chi tiết</a>
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
                <select class="w-filter-full" name="type">
                    <option>Loại thông báo</option>
                    <option>Ví điện tử</option>
                    <option>Đơn hàng</option>
                    <option>Khiếu nại</option>
                    <option>Vận đơn</option>
                </select>
                <select class="w-filter-full" name="status">
                    <option>Trạng thái</option>
                    <option>Chưa xem</option>
                    <option>Đã xem</option>
                </select>
                <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="mt-3">
                Số thông báo: <strong><?php echo count($notifications) ?></strong>
                <div class="table-responsive">
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
                            <?php foreach ($notifications as $key => $notification) {
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $notification->created_at);
                            ?>
                                <tr class="<?php echo ($notification->is_read === '0' ? "no-read" : '') ?>">
                                    <td class="text-center"><?php echo $key + 1 ?></td>
                                    <td><?php echo $date->format('d/m/Y H:i') ?></td>
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
</div>

<script>
    $('.no-read').on("mouseover", function() {
        $(this).removeClass('no-read')
    })
</script>