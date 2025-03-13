<div class="dashboard">
    <div class="d-flex w-100 gap-2">
        <div class="mt-3 flex-3 align-items-stretch">
            <h4 class="text-uppercase">Ví điện tử</h4>
            <div class="notification-dashboard" style="height: 150px">
                <div class="d-flex align-items-center justify-content-between">
                    <div>Số dư trong ví: <strong style="color: #ff0000">0</strong> VNĐ</div>
                    <a target="__blank" href="<?php echo site_url() . '/nap-tien' ?>" class="btn btn-primary">Nạp tiền</a>
                </div>
                <div>Mã nạp tiền: <strong style="color: #ff0000">HK_<?php echo get_current_user_id() ?>_NT</strong></div>
                <div style="font-size: 12px" class="mt-2">
                    Tổng tiền hàng đã về chờ tất toán : <strong style="color: #ff0000">0</strong> đ
                </div>
                <div style="font-size: 12px">Tổng tiền hàng chưa về : <strong style="color: #ff0000">0</strong> đ</div>
            </div>
        </div>
        <div class="mt-3 flex-1">
            <h4 class="text-uppercase">Tài chính</h4>
            <div class="notification-dashboard" style="height: 150px">
                <div class="mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền nạp : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng chi tiêu : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tổng tiền đơn hàng : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Tiền đang cọc : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted mb-1 d-flex justify-content-between" style="font-size: 12px">Cần thanh toán : <span><strong style="color: #ff0000">0</strong> đ</span></div>
                <div class="border-dotted"></div>
            </div>
        </div>
    </div>
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Nạp tiền vào ví điện tử</h4>
        <div class="notification-dashboard">
            <div class="d-flex gap-4">
                <img src="<?php echo get_template_directory_uri() . '/images/bank.png' ?>" />
                <div>
                    <h6>MB NGÂN HÀNG QUÂN ĐỘI</h6>
                    <div>Số tài khoản: <strong>868199533333</strong></div>
                    <div>Chủ tài khoản: <strong>Lê Kim Trường</strong></div>
                    <div>Nội dung chuyển khoản: <strong style="color: green">HK_<?php echo get_current_user_id() ?>_NT</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3 text-uppercase flex-1">
        <h4>Lịch sử giao dịch</h4>
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
                    <option>Chờ đặt cọc (0)</option>
                    <option>Chờ mua hàng (0)</option>
                    <option>Đang mua hàng (0)</option>
                    <option>Chờ shop phát hàng (0)</option>
                    <option>Shop TQ Phát hàng (0)</option>
                    <option>Kho TQ nhận hàng (0)</option>
                    <option>Xuất kho TQ (0)</option>
                    <option>Trong kho VN (0)</option>
                    <option>Sẵn sàng giao hàng (0)</option>
                    <option>Chờ xử lý khiếu nại (0)</option>
                    <option>Đã kết thúc (0)</option>
                    <option>Đã hủy (0)</option>
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