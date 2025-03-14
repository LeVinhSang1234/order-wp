<div class="dashboard">
    <div class="mt-3 text-uppercase flex-1">
        <h4>DANH SÁCH KHIẾU NẠI SHOP</h4>
        <div class="notification-dashboard">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input class="w-filter-full" placeholder="Mã đơn hàng" />
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
                Số khiếu nại: <strong>0</strong>
                <div class="table-responsive">
                <table class="w-100 mt-2" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th >#</th>
                            <th>Thông tin đơn hàng</th>
                            <th>Thông tin tài chính</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>