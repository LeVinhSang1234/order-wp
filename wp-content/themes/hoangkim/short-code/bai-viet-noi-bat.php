<?php
function mytheme_baiviet_shortcode()
{

    $Posts = [];
    if (is_single()) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                $Posts[] = array(
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                );
            endwhile;
            wp_reset_postdata();
        endif;
    }

    $string = '';

    foreach ($Posts as $post) {
        $string .=  '<a class="custom-post-card" href="' . $post['permalink'] . '">
            <img src="' . $post['thumbnail'] . '" alt="' . $post['title'] . '" class="custom-post-thumbnail">
            <div class="custom-post-title">' . $post['title'] . '</div>
        </a>';
    }

    return '<div class="mt-4">
        <h4 class="pt-2">Bài viết nổi bật</h4> ' .
        $string .
        '<div>';
}
add_shortcode('baiviet', 'mytheme_baiviet_shortcode');
