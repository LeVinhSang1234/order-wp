<?php
$page_id = get_the_ID();
$page = get_post($page_id);
$title = $page->post_title;
$post_content = $page->post_content;
get_header();
?>

<body>
    <main>
        <div class="banner">
            <div class="container">
                <div class="banner-download-wrap">
                    <div class="banner-download">
                        <img width="200px" class="logo" src="<?php echo get_option('custom_logo'); ?>" alt="Hoàng Kim Logo" />
                        <div class="sub_title">CÔNG CỤ ĐẶT HÀNG TRUNG QUỐC </div>
                        <div class="desc_">(Lưu ý: Chỉ sử dụng trên máy tính)</div>
                        <div class="list_link">
                            <a href="todo" class="link" rel="nofollow">
                                <div class=" img-wrap">
                                    <img src="<?php echo site_url() . '/wp-content/uploads/2025/02/chrome.png' ?>" alt="" data-ll-status="loaded" class="entered lazyloaded" />
                                </div>
                                <div class="desc">Tải về cho trình duyệt <br>Google Chrome </div>
                            </a>
                            <a href="todo" class="link" rel="nofollow">
                                <div class=" img-wrap">
                                    <img src="<?php echo site_url() . '/wp-content/uploads/2025/02/coccoc.png' ?>" alt="" data-ll-status="loaded" class="entered lazyloaded" />
                                </div>
                                <div class="desc">Tải về cho trình duyệt <br>Cốc Cốc </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo apply_filters('the_content', $post_content);
        ?>
    </main>
    <?php get_footer(); ?>
</body>

</html>