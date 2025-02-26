<?php
function mytheme_bang_gia_shortcode()
{
    return '<section class="price-list-wrapper">
            <div class="container">
                <h2 class="title">BẢNG GIÁ DỊCH VỤ NHẬP HÀNG TỪ TRUNG QUỐC</h2>
                <div class="content">
                    <div class="image">
                        <img src="https://haitau.vn/wp-content/uploads/2024/05/image-price-list.png" alt="" data-lazy-src="https://haitau.vn/wp-content/uploads/2024/05/image-price-list.png" data-ll-status="loaded" class="entered lazyloaded"><noscript><img src="https://haitau.vn/wp-content/uploads/2024/05/image-price-list.png" alt=""></noscript>
                    </div>

                    <div class="list">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="item">
                                    <div class="index">
                                        1 </div>
                                    <div class="name">
                                        TIỀN HÀNG TRÊN WEB </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="item">
                                    <div class="index">
                                        2 </div>
                                    <div class="name">
                                        PHÍ SHIP TRUNG QUỐC </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="item">
                                    <div class="index">
                                        3 </div>
                                    <div class="name">
                                        PHÍ DỊCH VỤ </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="item">
                                    <div class="index">
                                        4 </div>
                                    <div class="name">
                                        CƯỚC VẬN CHUYỂN </div>
                                </div>
                            </div>
                        </div>
                        <div class="primary-button small"><a href="todo" rel="nofollow">Chi tiết bảng giá<i class="fa-solid fa-arrow-right-long"></i></a></div>
                    </div>
                </div>
            </div>
        </section>';
}
add_shortcode('bang-gia', 'mytheme_bang_gia_shortcode');
