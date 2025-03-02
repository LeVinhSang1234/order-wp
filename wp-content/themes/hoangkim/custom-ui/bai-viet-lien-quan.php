<?php

$Posts = [];
if ((is_single() || is_front_page()) && !is_page("dang-ki") && !is_page('dang-nhap')) {
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
<?php if (count($Posts) > 0) { ?>
    <div class="container mb-4">
        <h4 class="text-center title-border mt-5 mb-4 text-uppercase">
            <?php echo (!is_single() ? "Tin tức mới nhất" : "Bài viết liên quan") ?>
        </h4>
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