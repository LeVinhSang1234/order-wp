<?php
if (!is_user_logged_in()) {
    wp_redirect(site_url() . '/dang-nhap/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <?php wp_head(); ?>
    <title>Mua hàng</title>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/mua-hang-header.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/mua-hang-content.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/mua-hang-dashboard.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/mua-hang-cart.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/gio-hang.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/chi-tiet-don-hang.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/don-hang-ky-gui.css?' ?>" type="text/css" media="all" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css?">
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <main>
        <?php include_once get_template_directory() . '/mua-hang/header.php' ?>
        <div class="mua-hang-content">
            <div class="menu-left">
                <ul>
                    <li>
                        <a class="<?php echo (is_page('mua-hang') ? 'active' : '') ?>" href="<?php echo site_url() . '/mua-hang' ?>">
                            <i class="fa-solid fa-chart-simple"></i>
                            Bảng tin
                        </a>
                    </li>
                    
                    <li>
                        <a class="<?php echo (is_page('don-hang') ? 'active' : '') ?>" href="<?php echo site_url() . '/don-hang' ?>">
                            <i class="fa-regular fa-clipboard"></i>
                            Đơn hàng Order
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('don-hang-ky-gui') ? 'active' : '') ?>" href="<?php echo site_url() . '/don-hang-ky-gui' ?>">
                            <i class="fa-solid fa-van-shuttle"></i>
                            Đơn hàng ký gửi
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('don-ngoai-san') ? 'active' : '') ?>" href="<?php echo site_url() . '/don-ngoai-san' ?>">
                            <i class="fa-regular fa-clipboard"></i>
                            Đơn ngoài sàn
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('wallet') ? 'active' : '') ?>" href="<?php echo site_url() . '/wallet' ?>">
                            <i class="fa-solid fa-wallet"></i>
                            Ví điện tử
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('khieu-nai') ? 'active' : '') ?>" href="<?php echo site_url() . '/khieu-nai' ?>">
                            <i class="fa-solid fa-bug"></i>
                            Khiếu nại
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('gio-hang') ? 'active' : '') ?>" href="<?php echo site_url() . '/gio-hang' ?>">
                            <i class="fa-solid fa-cart-plus"></i>
                            Giỏ hàng
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('tai-khoan') ? 'active' : '') ?>" href="<?php echo site_url() . '/tai-khoan' ?>">
                            <i class="fa-solid fa-user"></i>
                            Tài khoản
                        </a>
                    </li>
                    <li>
                        <a class="<?php echo (is_page('doi-mat-khau') ? 'active' : '') ?>" href="<?php echo site_url() . '/doi-mat-khau' ?>">
                            <i class="fa-solid fa-lock"></i>
                            Đổi mật khẩu
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo wp_logout_url(home_url())?>">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
            <div class="mua-hang-content-right">
                <div class="mua-hang-content-right__content">
                    <?php include_once get_template_directory() . '/mua-hang/redirect.php' ?>
                </div>
                <div class="mua-hang-footer">
                    Copyright © 2025 HoangKimLogistics.VN. All Rights Reserved
                </div>
            </div>
        </div>
    </main>
</body>

</html>

<script>
    $(document).ready(function() {

        $(window).resize(function() {
            if ($(window).width() < 576) {
                $('.menu-left').addClass('menu-left-close');
            }
        });

        $('.menu-toggle').on('click', function(event) {
            event.stopPropagation();
            $(".menu-toggle-close").addClass("menu-open")
            $(this).addClass("menu-open")
            $('.menu-left').addClass('menu-left-close');
        })

        $('.menu-toggle-close').on('click', function(event) {
            event.stopPropagation();
            $('.menu-toggle').removeClass('menu-open')
            $(".menu-toggle-close").removeClass("menu-open")
            $('.menu-left').removeClass('menu-left-close');
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('.menu-left, .menu-toggle').length && $(window).width() < 769) {
                $('.menu-left').addClass('menu-left-close');
            }
        });

    })
</script>