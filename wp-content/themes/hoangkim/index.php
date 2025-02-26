<?php
$page_id = get_the_ID();
$page = get_post($page_id);
$title = $page->post_title;
$post_content = $page->post_content;
get_header();
?>

<body>
    <main>
        <?php
        echo apply_filters('the_content', $post_content);
        ?>
    </main>
    <?php get_footer(); ?>
</body>

</html>