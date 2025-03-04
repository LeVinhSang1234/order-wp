<?php
function create_muahang_page()
{
    // Kiểm tra xem trang "mua-hang" đã tồn tại hay chưa
    $page = get_page_by_path('mua-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Mua Hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'mua-hang', // Đường dẫn
        );
        wp_insert_post($new_page);
    }

    $page = get_page_by_path('don-hang');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Đơn Hàng', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'don-hang', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('wallet');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Ví điện tử', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'wallet', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
    $page = get_page_by_path('khieu-nai');
    if (!$page) {
        // Tạo trang mua-hang
        $new_page = array(
            'post_title'    => 'Khiếu nại', // Tiêu đề trang
            'post_content'  => '',
            'post_status'   => 'publish', // Đặt trang thành "đã xuất bản"
            'post_author'   => 1, // ID của tác giả (1 là admin mặc định)
            'post_type'     => 'page', // Kiểu bài viết là "page"
            'post_name'     => 'khieu-nai', // Đường dẫn
        );
        wp_insert_post($new_page);
    }
}
add_action('after_setup_theme', 'create_muahang_page');
