<?php

function mytheme_quy_trinh_nhap_hang_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'image-src-1' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-2' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-3' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-4' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-5' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-6' => '/wp-content/uploads/2025/03/bao-gia.jpg',
            'image-src-board' => 'https://hoangkimlogistics.vn/wp-content/uploads/2025/03/boad.png',
        ),
        $atts,
        'quy-trinh-nhap-hang'
    );

    return '<div class="container">
                <div class="quy-trinh-nhap-hang">
                    <h4 class="title-border mb-3">QUY TRÌNH NHẬP HÀNG TRUNG QUỐC</h4>
                    <div class="tab-wrapper">
                        <div class="wrapper-nav">
                            <div class="head" id="pills-tab" role="tablist">
                                <div class="menu-item active" data-title="Đăng ký tài khoản" data-url="' . $atts['image-src-1'] . '">
                                    <div class="tab-title">Đăng ký tài khoản</div>
                                </div>
                                <div class="menu-item" data-title="Cài đặt công cụ mua hàng" data-url="' . $atts['image-src-2'] . '">
                                    <div class="tab-title">Cài đặt công cụ mua hàng</div>
                                </div>
                                <div class="menu-item" data-title="Chọn &amp; thêm vào giỏ hàng" data-url="' . $atts['image-src-3'] . '">
                                    <div class="tab-title">Chọn &amp; thêm vào giỏ hàng</div>
                                </div>
                                <div class="menu-item" data-title="Gửi đơn đặt hàng" data-url="' . $atts['image-src-4'] . '">
                                    <div class="tab-title">Gửi đơn đặt hàng</div>
                                </div>
                                <div class="menu-item" data-title="Đặt cọc đơn hàng" data-url="' . $atts['image-src-5'] . '">
                                    <div class="tab-title">Đặt cọc đơn hàng</div>
                                </div>
                                <div class="menu-item" data-title="Nhận hàng &amp; thanh toán" data-url="' . $atts['image-src-6'] . '">
                                    <div class="tab-title">Nhận hàng &amp; thanh toán</div>
                                </div>
                            </div>
                        </div>
                        <div class="image-view">
                            <p class="title-view mb-2">Đăng ký tài khoản</p>
                            <img class="image-wrap" src="' . $atts['image-src-1'] . '" />
                        </div>
                        <div class="background-image">
                            <img src="' . $atts['image-src-board'] . '"/>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $(".quy-trinh-nhap-hang .wrapper-nav .menu-item").on("click", function() {
                            if (!$(this).hasClass("active")) {
                                $(".quy-trinh-nhap-hang .wrapper-nav .menu-item").removeClass("active")
                                $(this).addClass("active")
                                const title = $(this).attr("data-title")
                                $(".quy-trinh-nhap-hang .image-view .title-view").html(title)
                            }
                        })
                    })
                </script>
            </div>';
}

add_shortcode('quy-trinh-nhap-hang', 'mytheme_quy_trinh_nhap_hang_shortcode');
