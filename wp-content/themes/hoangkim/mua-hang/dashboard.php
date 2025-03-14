<?php
global $wpdb;
$user_id = get_current_user_id();
$total_orders = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}orders WHERE user_id = %d",
    $user_id
));
$total_cart = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}cart WHERE user_id = %d and is_done = 0",
    $user_id
));
?>

<div class="dashboard">
    <div class="row">
        <div class="col-lg-3 col-md-6 pb-2">
            <div class="box-dashboard box-dashboard-green">
                <h4>0 đ</h4>
                <div class="title">Số dư</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 pb-2">
            <div class="box-dashboard box-dashboard-aqua">
                <h4><?php echo $total_orders; ?> Đơn</h4>
                <div class="title">Đơn hàng</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 pb-2">
            <div class="box-dashboard box-dashboard-cart">
                <h4><?php echo $total_cart; ?> sản phẩm</h4>
                <div class="title">Giỏ hàng</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 pb-2">
            <div class="box-dashboard box-dashboard-report">
                <h4>...</h4>
                <div class="title">Khiếu nại</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
    </div>
    <div class="mt-3 text-uppercase flex-1">
        <h4>Thông báo</h4>
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
                Số thông báo: <strong>0</strong>
                <div class="table-responsive">
                <table class="w-100 mt-2" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thời gian</th>
                            <th>Loại thông báo</th>
                            <th>Nội dung</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>