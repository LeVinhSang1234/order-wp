<?php

function mytheme_step_shortcode()
{
    return '<div class="step">
            <div class="container">
                <div class="step-border"></div>
                <div class="row row-cols-lg-5 row-cols-md-5">
                    <div class="col-sm-6 col-12 mb-4">
                        <div class="process-item active">
                            <div class="item">
                                <div class="icon img-wrap">
                                    <img src="/wp-content/uploads/2025/02/tracking.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/tracking.png" data-ll-status="loaded" class="entered lazyloaded"><noscript><img src="/wp-content/uploads/2025/02/tracking.png" alt=""></noscript>
                                </div>
                                <div class="name">
                                    TÌM KIẾM<br>SẢN PHẨM </div>
                            </div>
                            <div class="steps-process">
                                <img src="/wp-content/uploads/2025/02/steps.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded"><noscript><img src="/wp-content/uploads/2025/02/steps.png" alt=""></noscript>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12 mb-4">
                        <div class="process-item active">
                            <div class="item">
                                <div class="icon img-wrap">
                                    <img src="/wp-content/uploads/2025/02/packing-list.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded"><noscript><img src="/wp-content/uploads/2025/02/packing-list.png" alt=""></noscript>
                                </div>
                                <div class="name">
                                    TẠO <br>ĐƠN HÀNG </div>
                            </div>
                            <div class="steps-process">
                                <img src="/wp-content/uploads/2025/02/steps.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded"><noscript><img src="/wp-content/uploads/2025/02/steps.png" alt=""></noscript>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12 mb-4">
                        <div class="process-item active">
                            <div class="item">
                                <div class="icon img-wrap">
                                    <img src="/wp-content/uploads/2025/02/tracking.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded" />
                                </div>
                                <div class="name">
                                    ĐẶT CỌC <br>TIỀN HÀNG </div>
                            </div>
                            <div class="steps-process">
                                <img src="/wp-content/uploads/2025/02/steps.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12 mb-4">
                        <div class="process-item active">
                            <div class="item">
                                <div class="icon img-wrap">
                                    <img src="/wp-content/uploads/2025/02/delivery-truck.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/delivery-truck.png" data-ll-status="loaded" class="entered lazyloaded">
                                </div>
                                <div class="name">
                                    THEO DÕI<br> ĐƠN HÀNG </div>
                            </div>
                            <div class="steps-process">
                                <img src="/wp-content/uploads/2025/02/steps.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/steps.png" data-ll-status="loaded" class="entered lazyloaded">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12 mb-4">
                        <div class="process-item active">
                            <div class="item">
                                <div class="icon img-wrap">
                                    <img src="/wp-content/uploads/2025/02/package.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/package.png" data-ll-status="loaded" class="entered lazyloaded">
                                </div>
                                <div class="name">
                                    THANH TOÁN &amp;<br> NHẬN HÀNG </div>
                            </div>
                            <div class="steps-process">
                                <img src="/wp-content/uploads/2025/02/checked.png" alt="" data-lazy-src="/wp-content/uploads/2025/02/checked.png" data-ll-status="loaded" class="entered lazyloaded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}
add_shortcode('step-header', 'mytheme_step_shortcode');
