<?php
function mytheme_banner_shortcode()
{
    return '<div class="banner">' .
        '<div class="container">' .
        '<div class="banner-download-wrap">' .
        '<div class="banner-download banner-download__find-product">' .
        '<div class="sub_title">DỊCH VỤ NHẬP HÀNG TRUNG QUỐC</div>' .
        '<div class="box-select-find">' .
        '<select>' .
        '<option>TAOBAO.COM</option>' .
        '<option>1688.COM</option>' .
        '<option>TMALL.COM</option>' .
        '</select>' .
        '<input placeholder="Tìm kiếm sản phẩm" />' .
        '<div class="btn-find">' .
        '<i class="fas fa-search"></i>' .
        '</div>' .
        '</div>' .
        '</div>' .
        '<div class="banner-download">' .
        '<div class="sub_title">CÔNG CỤ ĐẶT HÀNG TRUNG QUỐC </div>' .
        '<div class="desc_">(Lưu ý: Chỉ sử dụng trên máy tính)</div>' .
        '<div class="list_link">' .
        '<a href="todo" class="link" rel="nofollow">' .
        '<div class=" img-wrap">' .
        "<img src='" . site_url() . "/wp-content/uploads/2025/02/chrome.png'" . 'alt="" data-ll-status="loaded" class="entered lazyloaded" />' .
        '</div>' .
        '<div class="desc">Tải về cho trình duyệt <br>Google Chrome </div>' .
        '</a>' .
        '<a href="todo" class="link" rel="nofollow">' .
        '<div class=" img-wrap">' .
        "<img src='" . site_url() . "/wp-content/uploads/2025/02/coccoc.png' alt=\"\" data-ll-status=\"loaded\" class=\"entered lazyloaded\" />" .
        '</div>' .
        '<div class="desc">Tải về cho trình duyệt <br>Cốc Cốc </div>' .
        '</a>' .
        '</div>' .
        '</div>' .
        '</div>' .
        '</div>' .
        '</div>';
}
add_shortcode('banner-header', 'mytheme_banner_shortcode');
