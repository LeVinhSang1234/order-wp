<?php
function mytheme_login_shortcode()
{
    global $message;
    $email = '';
    if (isset($_POST['login']) && $_POST['login'] === 'Đăng ký') {
        $email = sanitize_email($_POST['username']);
    }
    $content = $message ? "<div class='text-error'>" . $message . "</div>" : "";

    return '<div class="sec-user">
    <div class="right-main">
        <div class="form-tab">
            <a class="active">Đăng nhập</a><a href="/dang-ki">Đăng ký</a>
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
                <div class="checkbox">
                    <a class="pull-right" href="todo">Quên mật khẩu?</a>
                </div>
                <div class="form-group text-center">
                    <input type="hidden" name="pre_url" value="">
                    <input type="submit" class="btn btn-danger" name="login" value="Đăng nhập">
                </div>
            </form>
            <hr>
            <div class="box_extension box_left">
                <p class="sbtitle">Công cụ đặt hàng</p>
                <div class="addon text-center">
                   <a rel="nofollow" href="https://chromewebstore.google.com/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-ho%C3%A0ng-ki/bjledagpiehdnlcjjcoafkijhggfpeag" target="_blank"><img src="/wp-content/uploads/2025/03/chrome.png"></a>
                    <a rel="nofollow" href="https://chromewebstore.google.com/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-ho%C3%A0ng-ki/bjledagpiehdnlcjjcoafkijhggfpeag" target="_blank"><img src="/wp-content/uploads/2025/03/coccoc.png"></a>
                </div>
            </div>
        </div>
    </div>
</div>';
}

add_shortcode('login', 'mytheme_login_shortcode');
