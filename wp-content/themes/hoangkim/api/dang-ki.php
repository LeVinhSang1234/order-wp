<?php
function custom_user_registration()
{
    if (isset($_POST['login']) && $_POST['login'] === 'Đăng ký') {
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        if ($password !== $confirm_password) {
            return 'Mật khẩu không khớp';
        }
        if (username_exists($email)) {
            return 'Tên người dùng đã tồn tại!';
        }
        if (email_exists($email)) {
            return 'Email đã tồn tại!';
        }
        $userdata = array(
            'user_login' => $email,
            'user_email' => $email,
            'user_pass'  => $password,
            'role'       => 'subscriber',
        );

        $user_id = wp_insert_user($userdata);
        if (!is_wp_error($user_id) && $phone) {
            update_user_meta($user_id, 'user_phone', $phone);
        } else {
            $error_message = $user_id->get_error_message();
            return 'Lỗi khi tạo người dùng: ' . $error_message;
        }
        $user = get_user_by('id', $user_id);
        $login_data = array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => true
        );
        $user = wp_signon($login_data, false);
        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
            return $error_message;
        }
        wp_redirect('/mua-hang');
        return;
    }
    if (isset($_POST['login']) && $_POST['login'] === 'Đăng nhập') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $login_data = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      =>  true
        );
        $user = wp_signon($login_data, false);
        if (is_wp_error($user)) {
            return "Sai tài khoản hoặc mật khẩu";
        } else {
            wp_redirect('/mua-hang');
            return;
        }
    }
}
