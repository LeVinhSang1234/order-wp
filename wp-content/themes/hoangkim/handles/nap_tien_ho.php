<?php

function render_nap_tien_ho_page()
{
    global $wpdb;

    // Kiểm tra nếu form được submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['amount'])) {
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $note = 'Nạp tiền'; // Ghi chú mặc định

        // Lấy số dư hiện tại của người dùng
        $current_wallet = get_user_meta($user_id, 'user_wallet', true);
        $current_wallet = $current_wallet ? floatval($current_wallet) : 0;

        // Cộng số tiền vào số dư
        $new_wallet = $current_wallet + $amount;

        // Cập nhật số dư người dùng
        update_user_meta($user_id, 'user_wallet', $new_wallet);

        // Thêm giao dịch vào bảng `wallet_transaction`
        $table_name = $wpdb->prefix . 'wallet_transaction';
        $current_user_id = get_current_user_id(); // Lấy ID người thực hiện
        $wpdb->insert($table_name, [
            'user_id' => $user_id,
            'so_tien' => $amount,
            'ma_phieu_thu' => uniqid('PT_'), // Tạo mã phiếu thu tự động
            'ghi_chu' => $note,
            'hinh_anh' => null, // Có thể cập nhật sau nếu cần
            'da_xu_ly' => 1, // Đã xử lý
            'nguoi_thuc_hien' => $current_user_id, // Thêm người thực hiện
        ]);

        // Hiển thị thông báo thành công
        echo '<div class="updated"><p>Nạp tiền thành công! Số dư mới của người dùng là: ' . number_format($new_wallet, 0, ',', '.') . ' VND</p></div>';
    }

    // Lấy danh sách người dùng
    $users = get_users();

    // Hiển thị form nạp tiền
    echo '<div class="wrap"><h1>Nạp Tiền Hộ</h1>';
    echo '<form method="post" action="">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="user_id">Select người dùng</label></th>';
    echo '<td>';
    echo '<select name="user_id" id="user_id" required>';
    echo '<option value="">-- Chọn người dùng --</option>';
    foreach ($users as $user) {
        echo '<option value="' . esc_attr($user->ID) . '">' . esc_html('MS' . ($user->ID < 10 ? '0' . $user->ID : $user->ID)) . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label for="amount">Số Tiền</label></th>';
    echo '<td><input type="number" name="amount" id="amount" required></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit"><button type="submit" class="button button-primary">Nạp Tiền</button></p>';
    echo '</form>';
    echo '</div>';
}
