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
    echo '<th><label for="user_search">Tìm kiếm & chọn người dùng</label></th>';
    echo '<td>';
    echo '<div style="position: relative; width: 300px;">';
    echo '<input type="text" id="user_search" placeholder="Nhập mã số người dùng (VD: MS01, MS15)" style="width: 100%; padding: 8px; box-sizing: border-box;" autocomplete="off">';
    echo '<input type="hidden" name="user_id" id="user_id" required>';
    echo '<div id="user_dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; display: none; z-index: 1000;">';
    foreach ($users as $user) {
        $user_code = 'MS' . ($user->ID < 10 ? '0' . $user->ID : $user->ID);
        echo '<div class="user-option" data-user-id="' . esc_attr($user->ID) . '" data-user-code="' . esc_attr($user_code) . '" style="padding: 8px; cursor: pointer; border-bottom: 1px solid #eee;">' . esc_html($user_code) . ' - ' . esc_html($user->display_name) . '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label for="amount">Số Tiền</label></th>';
    echo '<td><input type="number" name="amount" id="amount" required></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit"><button type="submit" class="button button-primary">Nạp Tiền</button></p>';
    echo '</form>';

    // Thêm JavaScript để tìm kiếm người dùng
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var searchInput = document.getElementById("user_search");
        var hiddenInput = document.getElementById("user_id");
        var dropdown = document.getElementById("user_dropdown");
        var options = dropdown.getElementsByClassName("user-option");
        
        // Hiển thị dropdown khi focus vào input
        searchInput.addEventListener("focus", function() {
            dropdown.style.display = "block";
            filterOptions();
        });
        
        // Ẩn dropdown khi click ra ngoài
        document.addEventListener("click", function(e) {
            if (!e.target.closest("#user_search") && !e.target.closest("#user_dropdown")) {
                dropdown.style.display = "none";
            }
        });
        
        // Tìm kiếm và lọc options
        searchInput.addEventListener("input", function() {
            filterOptions();
        });
        
        function filterOptions() {
            var searchValue = searchInput.value.toLowerCase();
            var hasVisible = false;
            
            for (var i = 0; i < options.length; i++) {
                var option = options[i];
                var userCode = option.getAttribute("data-user-code").toLowerCase();
                var displayText = option.textContent.toLowerCase();
                
                if (userCode.includes(searchValue) || displayText.includes(searchValue)) {
                    option.style.display = "block";
                    hasVisible = true;
                } else {
                    option.style.display = "none";
                }
            }
            
            dropdown.style.display = hasVisible ? "block" : "none";
        }
        
        // Xử lý click vào option
        for (var i = 0; i < options.length; i++) {
            options[i].addEventListener("click", function() {
                var userId = this.getAttribute("data-user-id");
                var userCode = this.getAttribute("data-user-code");
                var displayName = this.textContent;
                
                searchInput.value = displayName;
                hiddenInput.value = userId;
                dropdown.style.display = "none";
            });
            
            // Hover effect
            options[i].addEventListener("mouseenter", function() {
                this.style.backgroundColor = "#f0f0f0";
            });
            
            options[i].addEventListener("mouseleave", function() {
                this.style.backgroundColor = "white";
            });
        }
    });
    </script>';

    echo '</div>';
}
