<?php
$user_id = get_current_user_id();
$action = isset($_POST['action']) ? sanitize_textarea_field($_POST['action']) : '';
$user_phone = isset($_POST['user_phone']) ? sanitize_textarea_field($_POST['user_phone']) : '';
$display_name = isset($_POST['display_name']) ? sanitize_textarea_field($_POST['display_name']) : '';
$user_address = isset($_POST['user_address']) ? sanitize_textarea_field($_POST['user_address']) : '';

$isSuccess = false;

if ($action === 'save') {
    $updated_data = array(
        'ID'           => $user_id,
        'display_name' => $display_name,
    );
    wp_update_user($updated_data);
    update_user_meta($user_id, 'user_phone', $user_phone);
    update_user_meta($user_id, 'user_address', $user_address);
    $isSuccess = true;
}
$current_user = wp_get_current_user();
if (!$display_name) {
    $display_name = $current_user->display_name;
}
?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Tài khoản</h4>
        <div class="notification-dashboard">
            <form class="w-100" action="" method="post">
                <input type="hidden" name="action" value="save" />
                <div class="d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Tài khoản</strong> <input disabled value="<?php echo $current_user->user_login ?>" style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">E-mail</strong> <input disabled value="<?php echo $current_user->user_email ?>" style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Điện thoại</strong> <input name="user_phone" value="<?php echo display_user_phone(); ?>" style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Họ và tên</strong> <input name="display_name" value="<?php echo $display_name ?>" style="width: 100%; max-width: 600px" />
                </div>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Địa chỉ</strong> <textarea name="user_address" style="width: 100%; max-width: 600px"><?php echo display_user_address() ?></textarea>
                </div>
                <?php if ($isSuccess) { ?>
                    <div class="mt-3 d-flex fs-13 gap-3 w-100">
                        <strong style="width: 200px; text-align: right"></strong> <span class="text-success">Đổi mật khẩu thành công</span>
                    </div>
                <?php } ?>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right"></strong> <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>