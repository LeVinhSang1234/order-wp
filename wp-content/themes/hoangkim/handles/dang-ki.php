<?php
$message = "";
if (isset($_POST['login'])) {
    $message = custom_user_registration();
}
if (is_user_logged_in() && (is_page("dang-ki") || is_page('dang-nhap'))) {
    wp_redirect('/mua-hang');
    exit;
}
