<?php
function mytheme_dang_ki_shortcode()
{
    global $message;
    $email = '';
    $phone = '';
    if (isset($_POST['login']) && $_POST['login'] === 'Đăng ký') {
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['username']);
    }

    $content = $message ? "<div class='text-error'>" . $message . "</div>" : "";

    return '<div class="sec-user">
    <div class="right-main">
        <div class="form-tab">
            <a href="/dang-nhap">Đăng nhập</a><a class="active" >Đăng ký</a>
        </div>
        <div class="form-wrap">
            <form action="" method="post" class="form_dangnhap">
                <div class="form-group">
                    <i class="fa fa-envelope"></i>
                    <input type="text" name="username" value="' . $email . '" class="form-control" placeholder="Email">
                </div>' . $content . '
                <div class="form-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" value="" class="form-control" placeholder="Mật khẩu">
                </div>
                <div class="form-group">
                    <i class="fa fa-lock fa-stack-2x"></i>
                    <input type="password" name="confirm_password" value="" class="form-control" placeholder="Nhập lại mật khẩu">
                </div>
                <div class="form-group">
                    <i class="fa fa-lock fa-stack-2x"></i>
                    <input name="phone" value="' . $phone . '" class="form-control" placeholder="Số điện thoại">
                </div>
                <div class="checkbox">
                    <a class="pull-right" href="todo">Quên mật khẩu?</a>
                </div>
                <div class="form-group text-center">
                    <input type="hidden" name="pre_url" value="">
                    <input type="submit" class="btn btn-danger" name="login" value="Đăng ký">
                </div>
            </form>
            <hr>
            <div class="box_extension box_left">
                <p class="sbtitle">Công cụ đặt hàng</p>
                <div class="addon text-center">
                    <a rel="nofollow" href="todo" target="_blank"><img src="/wp-content/uploads/2025/02/chrome.png"></a>
                    <a rel="nofollow" href="todo" target="_blank"><img src="/wp-content/uploads/2025/02/coccoc.png"></a>
                </div>
            </div>
        </div>
    </div>
</div>';
}

add_shortcode('dang-ki', 'mytheme_dang_ki_shortcode');
