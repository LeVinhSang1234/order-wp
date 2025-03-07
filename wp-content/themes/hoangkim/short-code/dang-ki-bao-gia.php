<?php

function mytheme_dang_ki_bao_gia_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'image-src' => '/wp-content/uploads/2025/03/bao-gia.jpg',
        ),
        $atts,
        'dang-ki-bao-gia'
    );

    return '<div class="container">
        <div class="row bao-gia">
            <div class="col-12 col-md-7 col-lg-7 p-0">
                <div class="img-wrap">
                    <img src="' . $atts['image-src'] . '" alt="Báo giá miễn phí" />
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-5 p-0">
                <div class="form-bao-gia-wrap">
                    <form action="/" method="post" aria-label="Form liên hệ" novalidate="novalidate" data-status="init">
                        <div class="title">
                            <p>Đăng ký nhận báo giá</p>
                        </div>
                        <p class="mb-2">và tìm nguồn hàng Miễn phí</p>
                        <p><span><input size="40" aria-invalid="false" placeholder="Họ và tên(*)" value="" type="text" name="text-499"></span><br>
                            <span><input size="40" aria-required="true" aria-invalid="false" placeholder="Số điện thoại(*)" value="" type="tel" name="tel-862"></span><br>
                            <span><textarea cols="40" rows="10" aria-required="true" aria-invalid="false" placeholder="Nội dung sản phẩm cần mua(*)" name="textarea-6"></textarea></span><br>
                            <input class="btn-submit-bao-gia" type="submit" value="Đăng ký ngay"><span class="wpcf7-spinner"></span>
                        </p>
                        <p><i>(Vui lòng để lại thông tin của bạn, chúng tôi sẽ hỗ trợ bạn ngay)</i>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>';
}

add_shortcode('dang-ki-bao-gia', 'mytheme_dang_ki_bao_gia_shortcode');
