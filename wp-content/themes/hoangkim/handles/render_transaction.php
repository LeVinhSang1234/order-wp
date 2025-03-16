<?php
function cap_nhat_trang_thai() {
  global $wpdb;

  if (!isset($_POST['id'])) {
    wp_send_json_error(['message' => 'ID không hợp lệ']);
    return;
}

$id = intval($_POST['id']);
  $query = "SELECT * FROM {$wpdb->prefix}wallet_transaction WHERE id = %d";
  $transaction = $wpdb->get_row($wpdb->prepare($query, $id));

if ($transaction) {
    $user_id = $transaction->user_id; 
    $amount = $transaction->so_tien; // Số tiền từ giao dịch

    $current_wallet = get_user_meta($user_id, 'user_wallet', true);
    $current_wallet = $current_wallet ? floatval($current_wallet) : 0;

    $new_wallet = $current_wallet + $amount;

    $table = $wpdb->prefix . 'wallet_transaction';

    $result = $wpdb->update($table, ['da_xu_ly' => 1], ['id' => $id]);

    if ($result !== false) {
        update_user_meta($user_id, 'user_wallet', $new_wallet);
        wp_send_json_success([
            "message" => "Cập nhật thành công! Số dư mới: " . esc_html(number_format($new_wallet, 0, ',', '.')) . " VND",
            "errors" => 'Lỗi khi cập nhật'
          ]);
    
    } else {
        wp_send_json_error(['message' => 'Lỗi khi cập nhật']);
    }


   
} else {
    wp_send_json_error(['message' => 'Không tìm thấy giao dịch!']);
}
}

add_action('wp_ajax_cap_nhat_trang_thai', 'cap_nhat_trang_thai');

function render_nap_tien_page()
{
    global $wpdb;
    
    $query = "SELECT * FROM {$wpdb->prefix}wallet_transaction ORDER BY created_at DESC";
    $wallets = $wpdb->get_results($query);

    echo '<div class="wrap"><h2>Nạp Tiền</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th style="color: white">Mã phiếu thu</th>
                <th style="color: white">Thông tin đơn hàng</th>
                <th style="color: white">Thông tin tài chính</th>
                <th style="color: white">Trạng thái đơn hàng</th>
                <th style="color: white">Thao tác</th>
            </tr>
          </thead>';
    echo '<tbody>';

    foreach ($wallets as $wallet) {
        echo '<tr id="row-' . esc_attr($wallet->id) . '">
                <td>' . esc_html($wallet->ma_phieu_thu) . '</td>
                <td>--</td>
                <td>' . esc_html(number_format($wallet->so_tien, 0, ',', '.')) . ' VND</td>
                <td id="status-' . esc_attr($wallet->id) . '">' . 
                    ($wallet->da_xu_ly == "0" ? '<span style="color:red;">Chờ duyệt</span>' : '<span style="color:green;">Đã duyệt</span>') . 
                '</td>
                <td>
                    ' . ($wallet->da_xu_ly == "0" ? '<button class="xac-nhan-btn button-primary" data-id="' . esc_attr($wallet->id) . '">Xác nhận</button>' : 'Đã duyệt') . '
                </td>
              </tr>';
    }

    echo '</tbody></table></div>';

    // Thêm JavaScript xử lý AJAX
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".xac-nhan-btn").forEach(function(button) {
                    button.addEventListener("click", function() {
                        let id = this.getAttribute("data-id");
                        let row = document.getElementById("row-" + id);
                        let statusCell = document.getElementById("status-" + id);

                        if (confirm("Bạn có chắc chắn muốn xác nhận đơn này?")) {
                            fetch("' . admin_url('admin-ajax.php') . '", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: "action=cap_nhat_trang_thai&id=" + id
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    statusCell.innerHTML = "<span style=\'color:green;\'>Đã duyệt</span>";
                                    button.remove(); // Xóa nút sau khi xác nhận
                                } else {
                                    alert("Lỗi: " + data.message);
                                }
                            })
                            .catch(error => {
                                console.error("Lỗi:", error);
                                alert("Có lỗi xảy ra!");
                            });
                        }
                    });
                });
            });
          </script>';
}
