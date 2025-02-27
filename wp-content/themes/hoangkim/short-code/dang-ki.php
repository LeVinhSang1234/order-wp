<?php
function mytheme_dang_ki_shortcode()
{
    return '<div class="sec-user">
    <div class="right-main">
        <div class="form-tab">
            <a href="/dang-nhap">Đăng nhập</a><a class="active" >Đăng ký</a>
        </div>
        <div class="form-wrap">
            <form action="" method="post" class="form_dangnhap">
                <div class="form-group">
                    <i class="fa fa-envelope"></i>
                    <input type="text" name="username" value="" class="form-control" placeholder="Email">
                </div>
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
                    <input name="phone" value="" class="form-control" placeholder="Số điện thoại">
                </div>
                <div class="checkbox">
                    <a class="pull-right" href="todo">Quên mật khẩu?</a>
                </div>
                <div class="form-group ">
                    <input type="hidden" id="deviceToken" value="" name="device_token">
                    <div class="g-recaptcha" data-sitekey="6LciVWEUAAAAAJ-uNC1YpswmFwr2NDp9dg1HF8li">
                        <div style="width: 304px; height: 78px;">
                            <div><iframe title="reCAPTCHA" width="304" height="78" role="presentation" name="a-3iobao35nnqu" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox allow-storage-access-by-user-activation" src="https://www.google.com/recaptcha/api2/anchor?ar=1&amp;k=6LciVWEUAAAAAJ-uNC1YpswmFwr2NDp9dg1HF8li&amp;co=aHR0cHM6Ly9tdWFoYW5nLmhhaXRhdS52bjo0NDM.&amp;hl=vi&amp;v=rW64dpMGAGrjU7JJQr9xxPl8&amp;size=normal&amp;cb=eq63mxqlpkup"></iframe></div><textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid rgb(193, 193, 193); margin: 10px 25px; padding: 0px; resize: none; display: none;"></textarea>
                        </div><iframe style="display: none;"></iframe>
                    </div>
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
