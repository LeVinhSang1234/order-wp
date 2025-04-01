<?php
global $wpdb;
$table_name = $wpdb->prefix . 'orders';
$user_id = get_current_user_id();
$orders = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC");

if (isset($_POST['submit_don_ngoai_san'])) {
  $table_name = $wpdb->prefix . 'orders';

  $link_san_pham = sanitize_text_field($_POST['link_san_pham']);
  $link_hinh_anh = sanitize_text_field($_POST['link_hinh_anh']);
  $mau_sac_kich_thuoc = sanitize_text_field($_POST['mau_sac_kich_thuoc']);
  $so_luong = intval($_POST['so_luong']);
  $gia_tien = floatval($_POST['gia_tien']);
  $yeu_cau_khac = sanitize_text_field($_POST['yeu_cau_khac']);

  $wpdb->insert(
    $table_name,
    [
      'user_id'            => $user_id,
      'link_san_pham'      => $link_san_pham,
      'type'               => 0,
      'link_hinh_anh'      => $link_hinh_anh,
      'mau_sac_kich_thuoc' => $mau_sac_kich_thuoc,
      'so_kien_hang'       => $so_luong,
      'phi_mua_hang'       => $gia_tien,
      'note'               => $yeu_cau_khac,
    ],
    ['%d', '%s', '%d', '%s', '%s', '%f', '%f', '%s']
  );

  if ($wpdb->insert_id) {
    echo "<script>alert('Tạo đơn hàng thành công!');window.location.href='/don-ngoai-san/'</script>";
  } else {
    echo "<script>alert('Có lỗi xảy ra khi tạo đơn hàng!')'</script>";
  }
}
?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">TẠO ĐƠN NGOÀI SÀN</h4>
        <div class="notification-dashboard">
            <form class="w-100" method="post" action="" enctype="multipart/form-data">
                <div class="d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Link sản phẩm</strong>
                    <input require type="text" name="link_san_pham" placeholder="Link sản phẩm..." required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Link Hình ảnh</strong>
                    <input require type="text" name="link_hinh_anh" placeholder="Link Hình ảnh..." required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Màu sắc, kích thước</strong>
                    <input require type="text" name="mau_sac_kich_thuoc" placeholder="Màu sắc, kích thước..." required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong class="mt-1" style="width: 200px; text-align: right">Số lượng</strong>
                    <input require type="number" name="so_luong" placeholder="Số lượng..." required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong class="mt-1" style="width: 200px; text-align: right">Giá tiền (¥)</strong>
                    <input require type="number" name="gia_tien" placeholder="Giá tiền (¥)..." required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong class="mt-1" style="width: 200px; text-align: right">Yêu cầu khác</strong>
                    <input require type="text" name="yeu_cau_khac" placeholder="Yêu cầu thêm" required
                        style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right"></strong>
                    <button type="submit" name="submit_don_ngoai_san" class="btn btn-primary">+ Tạo đơn hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

</script>