<!DOCTYPE html>
<html lang="vi">

<head>
    <?php wp_head(); ?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo get_bloginfo('description'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo get_template_directory_uri() . "/js/placeholder-search.js" ?>" defer=""></script>
</head>
<div class="bar-top">
    <div class="container">
        <div class="dang-ki">
            <a><i class="fa-classic fa-solid fa-right-to-bracket" aria-hidden="true"></i> Đăng kí</a>
            <div style="padding: 8px 0" class="h-full">
                <div class="divider-vertical bg-white"></div>
            </div>
            <a><i class="fa-classic fa-solid fa-user-plus" aria-hidden="true"></i> Đăng nhập</a>
        </div>
    </div>
</div>
<header class="header">
    <div class="container">
        <nav>
            <ul>
                <?php wp_nav_menu(['theme_location' => 'main_menu']); ?>
            </ul>
        </nav>
    </div>
</header>
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
        <div class="logo-wrap">
            <div class="hotline-header">
                <img src="<?php echo site_url() . '/wp-content/uploads/2025/02/hotline-icon.jpg' ?>" />
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