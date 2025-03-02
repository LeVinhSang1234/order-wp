<div class="dashboard">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="box-dashboard box-dashboard-green">
                <h4>0 đ</h4>
                <div class="title">Số dư</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="box-dashboard box-dashboard-aqua">
                <h4>0 Đơn</h4>
                <div class="title">Đơn hàng</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="box-dashboard box-dashboard-cart">
                <h4>0 sản phẩm</h4>
                <div class="title">Giỏ hàng</div>
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
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
            <select name="type">
                <option>Loại thông báo</option>
                <option>Ví điện tử</option>
                <option>Đơn hàng</option>
                <option>Khiếu nại</option>
                <option>Vận đơn</option>
            </select>
            <select name="status">
                <option>Trạng thái</option>
                <option>Chưa xem</option>
                <option>Đã xem</option>
            </select>
            <div class="mt-3">
                Số thông báo: <strong>0</strong>
                <table class="w-100 mt-2">
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
