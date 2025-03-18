<?php
if (is_page('doi-mat-khau') || is_page('tai-khoan') || is_page('nap-tien') || is_page('mua-hang') || is_page('don-hang') || is_page('don-hang-ky-gui') || is_page('don-ngoai-san') || is_page('don-thanh-toan-ho') ||  is_page('wallet') || is_page('khieu-nai') || is_page("gio-hang") || is_page('chi-tiet-don-hang')) {
    include_once get_template_directory() . '/muahang-template.php';
    exit;
}

$page_id = get_the_ID();
$page = get_post($page_id);
$title = $page->post_title;
$post_content = $page->post_content;
require_once get_template_directory() . '/handles/dang-ki.php';

$isShow = !is_page("dang-ki") && !is_page('dang-nhap');

?>
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
    <?php if (is_page("dang-ki") || is_page('dang-nhap')) { ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/dang-ki.css' ?>" type="text/css" media="all" />
    <?php } ?>
    <?php if (is_front_page() || is_home()) { ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/bao-gia.css' ?>" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/quy-trinh-nhap-hang.css' ?>" type="text/css" media="all" />
    <?php } ?>
</head>
<?php if ($isShow) get_header(); ?>
<?php if (is_user_logged_in() && $isShow) wp_admin_bar_render(); ?>

<body>
    <main>
        <div class="w-100">
            <?php
            echo apply_filters('the_content', $post_content);
            ?>
        </div>
        <?php require_once get_template_directory() . '/custom-ui/bai-viet-lien-quan.php'; ?>
    </main>
    <?php if ($isShow) get_footer(); ?>
</body>

</html>