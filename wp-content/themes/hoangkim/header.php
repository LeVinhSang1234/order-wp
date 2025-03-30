<div class="bar-top">
    <div class="container">
        <div class="dang-ki">
            <?php if (!is_user_logged_in()) { ?>
                <a href="<?php echo site_url() . '/dang-ki' ?>"><i class="fa-classic fa-solid fa-right-to-bracket" aria-hidden="true"></i> Đăng kí</a>
                <div style="padding: 8px 0" class="h-full">
                    <div class="divider-vertical bg-white"></div>
                </div>
                <a href="<?php echo site_url() . '/dang-nhap' ?>"><i class="fa-classic fa-solid fa-user-plus" aria-hidden="true"></i> Đăng nhập</a>
            <?php } ?>
            <?php if (is_user_logged_in()) { ?>
                <a href="<?php echo site_url() . '/mua-hang' ?>"><i class="fa-solid fa-bars-progress"></i> Quản lý</a>
                <div style="padding: 8px 0" class="h-full">
                    <div class="divider-vertical bg-white"></div>
                </div>
                <a href="<?php echo wp_logout_url(home_url()); ?>"><i class="fa-classic fa-solid fa-right-to-bracket" aria-hidden="true"></i> Đăng xuất</a>
            <?php } ?>
        </div>
    </div>
</div>
<div class="main-header">
    <div class="container">
        <div class="logo-wrap logo-left">
            <a href="<?php echo site_url() ?>">
                <img class="logo" src="<?php echo get_option('custom_logo'); ?>" alt="Hoàng Kim Logo" />
            </a>
        </div>
        <div class="input-search-wrap">
            <input class="header-input input-search" />
            <i class="fas fa-search"></i>
        </div>
        <div class="logo-wrap logo-right">
            <div class="hotline-header">
                <img src="<?php echo site_url() . '/wp-content/uploads/2025/03/hotline-icon.jpg' ?>" />
                <div>
                    <strong>Hotline hỗ trợ</strong>
                    <a class="fw-600" href="tel:<?php echo get_option('custom_phone') ?>">
                        <div><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', get_option('custom_phone')) ?></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="header header-desktop">
    <div class="container">
        <nav>
            <ul>
                <?php wp_nav_menu(['theme_location' => 'main_menu']); ?>
            </ul>
        </nav>
    </div>
</header>
<header class="header header-mobile">
    <nav>
        <div class="btn-open-menu">
            <i class="fa-solid fa-bars"></i>
        </div>
        <img class="logo" src="/wp-content/uploads/2025/03/logo-white-e1741403707596.png" alt="Hoàng Kim Logo" />
    </nav>
    <ul class="btn-open-menu-list">
        <?php wp_nav_menu(['theme_location' => 'main_menu']); ?>
    </ul>
</header>
<?php if (!is_home() && !is_front_page()) { ?>
    <div class="breadcrumb-wrapper">
        <div class="container">
            <?php
            if (function_exists('bcn_display')) {
                bcn_display();
            }
            ?>
        </div>
    </div>
<?php } ?>

<script>
    $(document).ready(function() {
        $('.header .btn-open-menu').on('click', function() {
            $('.header .btn-open-menu-list').toggleClass('open')
        })
    })
</script>