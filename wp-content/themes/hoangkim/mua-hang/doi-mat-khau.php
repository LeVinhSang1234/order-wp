<?php
$isSuccess = isset($_GET['change_success']) ? true : false;
$user_id = get_current_user_id();
$reset_password = isset($_POST['reset_password']) ? sanitize_textarea_field($_POST['reset_password']) : '';
$password = isset($_POST['password']) ? sanitize_textarea_field($_POST['password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? sanitize_textarea_field($_POST['confirm_password']) : '';
$error = "";
if ($reset_password === 'change' && $password && $reset_password) {
    if ($password !== $confirm_password) {
        $error = "Mật khẩu không khớp";
    } else {
        wp_set_password($password, $user_id);
        insert_notification("Thay đổi mật khẩu", "Bạn đã thay đổi mật khẩu thành công. Nếu không phải bạn hãy liên hệ với chúng tôi.", null);
        echo "<script>window.location.href='/doi-mat-khau?change_success=true'</script>";
        exit;
    }
}
?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Đổi mật khẩu</h4>
        <div class="notification-dashboard">
            <form class="w-100" action="" method="post">
                <div class=" d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Mật khẩu mới</strong> <input name="password" type="password" value="<?php echo $password ?>" style="width: 100%; max-width: 600px" />
                </div>
                <input type="hidden" name="reset_password" value="change" />
                <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right">Nhập lại mật khẩu</strong> <input name="confirm_password" value="<?php echo $confirm_password ?>" type="password" style="width: 100%; max-width: 600px" />
                </div>
                <?php if ($error) { ?>
                    <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                        <strong style="width: 200px; text-align: right"></strong> <span class="text-error"><?php echo $error ?></span>
                    </div>
                <?php } ?>
                <?php if ($isSuccess) { ?>
                    <div class="mt-3 d-flex align-items-center fs-13 gap-3 w-100">
                        <strong style="width: 200px; text-align: right"></strong> <span class="text-success">Đổi mật khẩu thành công</span>
                    </div>
                <?php } ?>
                <div class="mt-3 d-flex fs-13 gap-3 w-100">
                    <strong style="width: 200px; text-align: right"></strong> <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>