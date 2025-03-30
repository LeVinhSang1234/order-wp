<?php
function mytheme_banner_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'background_url' => '', // Default value for background URL
        ),
        $atts,
        'banner-header'
    );

    return '<div class="banner" style="background-image: url(' . esc_url($atts['background_url']) . ');">' .
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
        '<a href="https://chromewebstore.google.com/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-ho%C3%A0ng-ki/bjledagpiehdnlcjjcoafkijhggfpeag" class="link" rel="nofollow">' .
        '<div class=" img-wrap">' .
        "<img src='" . site_url() . "/wp-content/uploads/2025/03/chrome.png'" . 'alt="" data-ll-status="loaded" class="entered lazyloaded" />' .
        '</div>' .
        '<div class="desc">Tải về cho trình duyệt <br>Google Chrome </div>' .
        '</a>' .
        '<a href="https://chromewebstore.google.com/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-ho%C3%A0ng-ki/bjledagpiehdnlcjjcoafkijhggfpeag" class="link" rel="nofollow">' .
        '<div class=" img-wrap">' .
        "<img src='" . site_url() . "/wp-content/uploads/2025/03/coccoc.png' alt=\"\" data-ll-status=\"loaded\" class=\"entered lazyloaded\" />" .
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
