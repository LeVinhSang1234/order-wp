<?php
$page_id = get_the_ID();
$page = get_post($page_id);
$title = $page->post_title;
$post_content = $page->post_content;

$Posts = [];
if (is_single()) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 4,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();

            $parent_id = wp_get_post_parent_id(get_the_ID());
            $parent_title = $parent_id ? get_the_title($parent_id) : '';

            $Posts[] = array(
                'ID' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'excerpt' => get_the_excerpt(),
                'date' => get_the_date(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'parent_title' => $parent_title
            );
        endwhile;
        wp_reset_postdata();
    endif;
}
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

</head>
<?php if (!is_page("dang-ki") && !is_page('dang-nhap')) get_header(); ?>
<?php if (is_user_logged_in() && !is_page('dang-nhap')) wp_admin_bar_render(); ?>

<body>
    <main>
        <div class="w-100">
            <?php
            echo apply_filters('the_content', $post_content);
            ?>
        </div>
        <?php if (is_single()) { ?>
            <div class="container mb-4">
                <h4 class="text-center title-border mt-5 mb-4 text-uppercase">Bài viết liên quan</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 align-items-strech">
                    <?php if (!empty($Posts)) : ?>
                        <?php foreach ($Posts as $post) : ?>
                            <div class="col">
                                <div class="card post-card">
                                    <img src="<?php echo $post['thumbnail'] ?>" class="card-img-top post-img" alt="<?= esc_attr($post['title']); ?>">
                                    <div class="card-body">
                                        <h5 class="post-title mb-1">
                                            <a href="<?= esc_url($post['permalink']); ?>" class="text-decoration-none"><?= esc_html($post['title']); ?></a>
                                        </h5>
                                        <p class="post-meta mb-0">📅 <?= esc_html($post['date']); ?> | 📂 <?php echo $post['parent_title'] ?></p>
                                        <p class="post-excerpt"><?= esc_html($post['excerpt']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-center">Không có bài viết nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>
    </main>
    <?php if (!is_page("dang-ki")) get_footer(); ?>
</body>

</html>