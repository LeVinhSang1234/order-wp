<?php

function register_order_menu()
{
  add_menu_page("Quản lý đơn hàng", "Đơn hàng", "manage_options", "order_list", "render_order_page");
  add_submenu_page(null, "Chi tiết đơn hàng", "Chi tiết đơn hàng", "manage_options", "order_detail", "render_order_detail");
}
add_action("admin_menu", "register_order_menu");

function render_order_detail()
{
  global $wpdb;

  if (!isset($_GET['id'])) {
    echo '<div class="error"><p>Không tìm thấy đơn hàng!</p></div>';
    return;
  }

  $order_id = intval($_GET['id']);
  $order = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}orders WHERE id = $order_id");
  $cart_ids_array = json_decode($order->cart_ids, true);
  $placeholders = implode(',', array_fill(0, count($cart_ids_array), '%d'));
  $query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cart WHERE id IN ($placeholders)",
    ...$cart_ids_array
  );
  $carts = $wpdb->get_results($query);

  $packages = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}packages WHERE order_id = %d",
      $order_id
    )
  );

  if (!$order) {
    echo '<div class="error"><p>Đơn hàng không tồn tại!</p></div>';
    return;
  }

  $field_labels = [
    'id' => 'Mã đơn hàng',
    'user_id' => 'ID Người dùng',
    'status' => 'Trạng thái',
    'total_price' => 'Tổng tiền',
    'payment_method' => 'Phương thức thanh toán',
    'updated_at' => 'Ngày cập nhật',
    'note' => 'Ghi chú',
    'is_gia_co' => 'Gia cố',
    'is_kiem_dem_hang' => 'Kiểm đếm hàng',
    'is_bao_hiem' => 'Bảo hiểm',
    'ho_ten' => 'Họ tên',
    'phone' => 'Sđt',
    'da_thanh_toan' => 'Đã thanh toán',
    'da_hoan' => 'Đã hoàn',
    'phi_kiem_dem' => 'Phí kiểm đếm',
    'so_kien_hang' => 'Số kiện hàng',
    'phi_mua_hang' => 'Phí mua hàng',
    'address' => 'Địa chỉ',
    'van_don' => 'Vận đơn',
    'exchange_rate' => 'Tỷ giá',
    'phi_gia_co' => 'Phí gia cố(¥)',
    'phi_ship_noi_dia' => 'Phí ship nội địa(¥)',
    'chiet_khau_dich_vu' => 'Chiết khấu dịch vụ',
    "brand" => "Tên hàng hóa",
    'created_at' => 'Ngày tạo',
    "da_coc" => "Đã đặt cọc",
  ];

  $editable_fields = [
    'da_thanh_toan',
    'da_hoan',
    'phi_kiem_dem',
    "phi_ship_noi_dia",
    "phi_gia_co",
    "exchange_rate",
    "chiet_khau_dich_vu",
    "tien_van_chuyen",
    "kg_tinh_phi",
    "da_coc",
  ];

  $hidden_fields = [
    'so_kien_hang',
    'phi_mua_hang',
    "ngay_dat_coc",
    "da_mua_hang",
    "ngay_nhap_kho_tq",
    "ngay_nhap_kho_tq",
    "ngay_nhap_kho_vn",
    "ngay_nhan_hang",
    "ngay_ncc_phat_hang",
    "tien_van_chuyen",
    'kg_tinh_phi',
    "user_id",
    "cart_ids",
    "type"
  ];

  $checkbox_fields = ['is_gia_co', 'is_kiem_dem_hang', 'is_bao_hiem', 'da_coc'];

  $exchange_rate = isset($order->exchange_rate) ? $order->exchange_rate : null;
  if (!$exchange_rate) {
    $exchange_rate = floatval(get_option('exchange_rate', 1.0));
  }

  $status_color = '';
  switch (intval($order->status)) {
    case 1:
        $status_color = 'color: black;'; // Chờ báo giá
        break;
    case 2:
        $status_color = 'color: orange;'; // Đang mua hàng
        break;
    case 3:
        $status_color = 'color: green;';  // Đã mua hàng
        break;
    case 4:
        $status_color = 'color: blue;';   // NCC phát hàng
        break;
    case 5:
        $status_color = 'color: purple;'; // Nhập kho TQ
        break;
    case 6:
        $status_color = 'color: pink;';   // Nhập kho VN
        break;
    case 7:
        $status_color = 'color: lightgreen;'; // Khách nhận hàng
        break;
    case 8:
        $status_color = 'color: red;';    // Đơn hàng hủy
        break;
    case 9:
        $status_color = 'color: gray;';   // Đơn khiếu nại
        break;
  }

  echo "<div class='wrap'><h2>Chi tiết đơn hàng #{$order->id}</h2>";

  echo "<div class='order-card'>";

  $status_str = ["", "Chờ báo giá", 'Đang mua hàng', 'Đã mua hàng', 'NCC phát hàng', 'Nhập kho TQ', 'Nhập kho VN', 'Khách nhận hàng', 'Đơn hàng hủy', 'Đơn khiếu nại'];

  foreach ($order as $field => $value) {
    if (in_array($field, $hidden_fields)) {
      continue; // Skip hidden fields
    }
    $label = isset($field_labels[$field]) ? $field_labels[$field] : ucfirst(str_replace('_', ' ', $field));
    $editable = in_array($field, $editable_fields) ? "contenteditable='true'" : "";
    $class = in_array($field, $editable_fields) ? "class='editable'" : "";

    if ($field === 'status') {
        $value = isset($status_str[$value]) ? $status_str[$value] : $value;
    }

    if (in_array($field, $checkbox_fields)) {
      $checked = $value == 1 ? "checked" : "";
      echo "<div class='order-item'>
              <strong>{$label}:</strong>
              <div><input type='checkbox' data-id='{$order->id}' data-field='{$field}' {$checked}></div>
            </div>";
    } else {
      // Nếu là ô có thể chỉnh sửa
      $editable = in_array($field, $editable_fields) ? "contenteditable='true'" : "";
      $class = in_array($field, $editable_fields) ? "class='editable'" : "";
      echo "<div class='order-item'>
              <strong>{$label}:</strong>
              <div {$editable} {$class} data-id='{$order->id}' data-field='{$field}'>{$value}</div>
            </div>";
    }
  }

  echo "</div>";

  echo "<table class='w-100 mt-4 table-list-chi-tiet' style='width: 100%'>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Cửa hàng</th>
                    <th>Số lượng</th>
                    <th style='width: 120px;'>Đơn giá</th>
                    <th style='width: 120px;'>Tiền hàng</th>
                </tr>
            </thead>
            <tbody>";

            foreach ($carts as $cart) {
              $totalPrice = 0;
              $totalPrice += ($cart->price * $cart->quantity);
              // todo
              echo "<tr>
                      <td>
                          <div style='display: flex; align-items: center; gap: 8px;'>
                              <img width='40px' src='{$cart->product_image}' />
                              <div>
                                  <a href='{$cart->product_url}'>" .
                                    str_replace("https://", "", parse_url($cart->product_url, PHP_URL_HOST)) .
                                  "</a>
                                  <div>{$cart->size} <br> {$cart->color}</div>
                              </div>
                          </div>
                      </td>
                      <td>
                          <a href='{$cart->shop_url}'>{$cart->shop_id}</a>
                      </td>
                      <td>
                          <input " . ($order->status > 1 ? "disabled" : "") . " 
                              data-type='quantity-cart' 
                              data-item='{$cart->id}' 
                              value='{$cart->quantity}' />
                      </td>
                      <td>
                          {$cart->price}¥
                      </td>
                      <td>
                          " . format_price_vnd(($totalPrice * $exchange_rate) ?? 0) . "
                      </td>
                  </tr>";
          }

echo "</tbody>
</table>";

// Add new table for additional fields
echo "<table class='w-100 mt-4 table-list-chi-tiet' style='width: 100%; margin-top: 20px;' id='packagesTable'>
    <thead>
        <tr>
            <th>STT</th>
            <th>Mã kiện</th>
            <th>Cân nặng</th>
            <th>Thể tích</th>
            <th>Tiền Thể tích</th>
            <th>Trạng thái</th>
            <th>Thời gian</th>
            <th>Tùy chọn</th>
        </tr>
    </thead>
    <tbody>";

        foreach ($packages as $index => $package) {
        echo "<tr data-id='{$package->id}'>
            <td>" . ($index + 1) . "</td> <!-- Render serial number dynamically -->
            <td contenteditable='true' class='editable-package' data-field='ma_kien'>{$package->ma_kien}</td>
            <td contenteditable='true' class='editable-package' data-field='can_nang'>{$package->can_nang}</td>
            <td contenteditable='true' class='editable-package' data-field='the_tich'>{$package->the_tich}</td>
            <td contenteditable='true' class='editable-package' data-field='the_tich'>{$package->tien_the_tich}</td>
            <td contenteditable='true' class='editable-package' data-field='trang_thai_kien'>{$package->trang_thai_kien}
            </td>
            <td>{$package->created_at}</td>
            <td><button class='button-secondary delete-package' data-id='{$package->id}'>Xóa</button></td>
        </tr>";
        }

        echo "</tbody>
</table>";
echo "<button id='addPackageRow' class='button'>Thêm hàng</button>";

echo "<div class='order-item'>
    <strong>Trạng thái:</strong>
    <select id='statusDropdown' data-id='{$order->id}'>
     <option value='1' " . ($order->status == 1 ? " selected" : "" ) . ">Chờ báo giá</option>
        <option value='2' " . ($order->status == 2 ? " selected" : "" ) . ">Đang mua hàng</option>
      <option value='3' " . ($order->status == 3 ? "selected" : "") . ">Đã mua hàng</option>
        <option value='4' " . ($order->status == 4 ? " selected" : "" ) . ">NCC phát hàng</option>
      <option value='5' " . ($order->status == 5 ? "selected" : "") . ">Nhập kho TQ</option>
        <option value='6' " . ($order->status == 6 ? " selected" : "" ) . ">Nhập kho VN</option>
      <option value='7' " . ($order->status == 7 ? "selected" : "") . ">Khách nhận hàng</option>
    </select>
</div>";
echo "<button id='updateOrder' class='button-primary'>Cập nhật</button>";
echo "<a href='" . admin_url("admin.php?page=order_list") . "' class='button'>Quay lại danh sách</a>";
echo "</div>";

?>
<style>
.order-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    width: 100%;
    display: flex;
    overflow: auto;
    flex-wrap: wrap;
    gap: 16px;
}

.order-item {
    margin-bottom: 10px;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
    width: 200px;
}

.order-item:last-child {
    border-bottom: none;
}

.editable {
    display: block;
    padding: 4px;
    background: #f9f9f9;
    border-radius: 4px;
    cursor: pointer;
    height: 20px;
    border: 1px solid
}

.editable:focus {
    background: #e6f7ff;
    outline: none;
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
jQuery(document).ready(function($) {
    $("#updateOrder").click(function() {
        let updates = [];

        // Collect order updates
        $(".editable[contenteditable='true']").each(function() {
            updates.push({
                order_id: $(this).data("id"),
                field: $(this).data("field"),
                value: $(this).text().trim() || null
            });
        });

        $("input[data-type='quantity-cart']").each(function() {
            updates.push({
                cart_id: $(this).data("item"),
                field: "quantity",
                value: $(this).val().trim() || null
            });
        });

        $("input[type='checkbox']").each(function() {
            updates.push({
                order_id: $(this).data("id"),
                field: $(this).data("field"),
                value: $(this).is(":checked") ? 1 : 0
            });
        });

        const statusFieldMap = {
            2: "ngay_dat_coc",
            3: "da_mua_hang",
            4: "ngay_nhap_kho_tq",
            5: "ngay_nhap_kho_tq",
            6: "ngay_nhap_kho_vn",
            7: "ngay_nhan_hang"
        };

        const selectedStatus = $("#statusDropdown").val();
        const orderId = $("#statusDropdown").data("id");
        updates.push({
            order_id: orderId,
            field: "status",
            value: selectedStatus
        });

        if (statusFieldMap[selectedStatus]) {
            updates.push({
                order_id: orderId,
                field: statusFieldMap[selectedStatus],
                value: new Date().toISOString().slice(0, 19).replace('T', ' ')
            });
        }

        // Collect package updates
        let packageUpdates = [];
        $("#packagesTable tbody tr").each(function() {
            const packageId = $(this).data("id") || null;
            const orderId = <?php echo $order_id; ?>; // Pass the current order_id
            let packageData = {
                order_id: orderId
            };

            $(this).find(".editable-package").each(function() {
                const field = $(this).data("field");
                const value = $(this).is("select") ? $(this).val() : $(this).text()
                    .trim(); // Handle dropdowns
                packageData[field] = value;
            });

            if (packageId) {
                packageData.package_id = packageId;
            }

            packageUpdates.push(packageData);
        });

        // Send AJAX request for order updates
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: "POST",
            data: {
                action: "update_order_admin",
                updates: JSON.stringify(updates)
            },
            beforeSend: function() {
                console.log("Đang gửi yêu cầu cập nhật đơn hàng...");
            },
            success: function(response) {
                console.log("Phản hồi từ server (đơn hàng):", response);
                if (response.success) {
                    // Send AJAX request for package updates
                    $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: "POST",
                        data: {
                            action: "update_packages",
                            packages: JSON.stringify(packageUpdates)
                        },
                        beforeSend: function() {
                            console.log(
                                "Đang gửi yêu cầu cập nhật kiện hàng...");
                        },
                        success: function(response) {
                            console.log("Phản hồi từ server (kiện hàng):",
                                response);
                            if (response.success) {
                                alert("Cập nhật thành công!");
                            } else {
                                alert("Cập nhật kiện hàng thất bại! Lỗi: " +
                                    response.data.message);
                            }
                        },
                        error: function() {
                            alert("Có lỗi xảy ra khi cập nhật kiện hàng.");
                        }
                    });
                } else {
                    alert("Cập nhật đơn hàng thất bại! Lỗi: " + response.data.message);
                }
            },
            error: function() {
                alert("Có lỗi xảy ra khi cập nhật đơn hàng.");
            }
        });
    });

    // Add new row to the packages table
    $("#addPackageRow").click(function() {
        const newRow = `
          <tr>
            <td>#</td>
            <td contenteditable="true" class="editable-package" data-field="ma_kien"></td>
            <td contenteditable="true" class="editable-package" data-field="can_nang"></td>
            <td contenteditable="true" class="editable-package" data-field="the_tich"></td>
            <td contenteditable="true" class="editable-package" data-field="trang_thai_kien"></td>
            <td>--</td>
            <td><button class="button-secondary delete-package">Xóa</button></td>
          </tr>`;
        $("#packagesTable tbody").append(newRow);
    });

    // Handle deleting a package row
    $(document).on("click", ".delete-package", function() {
        const row = $(this).closest("tr");
        const packageId = $(this).data("id");

        if (packageId) {
            // Send AJAX request to delete the package
            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: "POST",
                data: {
                    action: "delete_package",
                    package_id: packageId
                },
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        alert("Xóa thành công!");
                    } else {
                        alert("Xóa thất bại! Lỗi: " + response.data.message);
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa.");
                }
            });
        } else {
            row.remove(); // Remove unsaved row
        }
    });
});
</script>
<?php
}
?>