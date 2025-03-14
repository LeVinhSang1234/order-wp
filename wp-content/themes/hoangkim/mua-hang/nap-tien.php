<?php
$isSuccess = isset($_GET['wallet_success']) ? true : false;
if (isset($_POST['submit_wallet_transaction']) && is_user_logged_in()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wallet_transaction';
    $user_id = get_current_user_id();

    $so_tien = floatval($_POST['so_tien']);
    $ma_phieu_thu = sanitize_text_field($_POST['ma_phieu_thu']);
    $ghi_chu = sanitize_textarea_field($_POST['ghi_chu']);
    $hinh_anh = '';

    if (!empty($_FILES['hinh_anh']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded_file = wp_handle_upload($_FILES['hinh_anh'], array('test_form' => false));
        if (!isset($uploaded_file['error'])) {
            $hinh_anh = $uploaded_file['url'];
        }
    }
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'so_tien' => $so_tien,
            'ma_phieu_thu' => $ma_phieu_thu,
            'ghi_chu' => $ghi_chu,
            'hinh_anh' => $hinh_anh
        ),
        array('%d', '%f', '%s', '%s', '%s')
    );
    echo "<script>alert('Tạo phiếu nạp thành công. Vui lòng chờ hệ thống xử lý!');window.location.href='/nap-tien?wallet_success=true'</script>";
    exit;
}
?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Nạp tiền</h4>
        <div class="notification-dashboard">
            <form class="w-100" method="post" action="" enctype="multipart/form-data">
                <div class="d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Số tiền đã chuyển khoản</strong>
                    <input require type="number" name="so_tien" placeholder="Chỉ nhập số" required style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong class="mt-1" style="width: 200px; text-align: right"></strong>
                    <span class="text-success" style="margin-top: -10px" id="append_money">--</span>
                </div>
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Mã phiếu thu</strong>
                    <input require type="text" name="ma_phieu_thu" placeholder="Số bút toán sau khi bạn chuyển khoản" required style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Ghi chú</strong>
                    <textarea name="ghi_chu" placeholder="Gợi ý: Nhập nội dung chuyển khoản chính xác." style="width: 100%; max-width: 600px"></textarea>
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong class="mt-1" style="width: 200px; text-align: right">Hình ảnh</strong>
                    <input require style="border: none; margin-left: -8px" type="file" name="hinh_anh" accept="image/*" />
                </div>
                <?php if ($isSuccess) { ?>
                    <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                        <strong style="width: 200px; text-align: right"></strong> <span class="text-success">Đổi mật khẩu thành công</span>
                    </div>
                <?php } ?>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right"></strong>
                    <button type="submit" name="submit_wallet_transaction" class="btn btn-primary">Gửi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatCurrencyVND(amount) {
        if (!amount) return '--'
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
        }).format(amount);
    }
    $('input[name="so_tien"]').on('keyup', function() {
        const val = $(this).val()
        $('#append_money').html(formatCurrencyVND(Number(val)))
    })
</script>