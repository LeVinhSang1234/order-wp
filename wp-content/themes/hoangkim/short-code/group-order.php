<?php
function mytheme_group_order_shortcode()
{
    return '<div class="group-order container">
            <div class="row mt-4 list">
                <div class="col-12 col-md-6 col-lg-6 mb-4">
                    <div class="service-item">
                        <div class="img-wrap">
                            <img src="/wp-content/uploads/2025/03/icon-order.jpg" alt="" data-lazy-src="/wp-content/uploads/2025/03/icon-order.jpg" data-ll-status="loaded" class="entered lazyloaded">
                        </div>
                        <div class="info">
                            <a href="todo">
                                <h3 class="title">Mua hộ hàng Trung Quốc </h3>
                            </a>

                            <div class="desc">
                                <p>Dịch vụ nhập hàng Trung Quốc tận gốc trên các sàn TMĐT như:</p>
                                <ul>
                                    <li><a href="todo" target="_blank" rel="noopener"><strong>Mua hàng 1688</strong></a></li>
                                    <li><a href="todo" target="_blank" rel="noopener"><strong>Đặt hàng Taobao</strong></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-4">
                    <div class="service-item">
                        <div class="img-wrap">
                            <img src="/wp-content/uploads/2025/03/icon-order.jpg" alt="" data-lazy-src="/wp-content/uploads/2025/03/icon-order.jpg" data-ll-status="loaded" class="entered lazyloaded">
                        </div>
                        <div class="info">
                            <a href="todo">
                                <h3 class="title">Vận chuyển hàng Trung Quốc </h3>
                            </a>

                            <div class="desc">
                                <p>Tối ưu quy trình vận chuyển, nhập hàng Trung – Việt với hệ thống kho bãi tiện nghi, đưa phí vận chuyển về mức thấp trên thị trường.</p>
                                <ul>
                                    <li>Hàng về sau 5-7 ngày</li>
                                    <li>Đền 100% nếu mất/vỡ do vận chuyển</li>
                                    <li>Hệ thống kho bãi quy mô</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-4">
                    <div class="service-item">
                        <div class="img-wrap">
                            <img src="/wp-content/uploads/2025/03/icon-order.jpg" alt="" data-lazy-src="/wp-content/uploads/2025/03/icon-order.jpg" data-ll-status="loaded" class="entered lazyloaded">
                        </div>
                        <div class="info">
                            <a href="todo">
                                <h3 class="title">Dẫn khách đi đánh hàng </h3>
                            </a>

                            <div class="desc">
                                <p>Phiên dịch thông thạo các địa chỉ nhập hàng uy tín. Ghép nhóm đánh hàng tiết kiệm chi phí cho các chủ thể kinh doanh</p>
                                <ul>
                                    <li>70% nhân sự Trung Quốc là người bản địa</li>
                                    <li>Quan hệ tốt với các xưởng nhập hàng</li>
                                    <li>Thông thạo địa hình các chợ nhập hàng</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-4">
                    <div class="service-item">
                        <div class="img-wrap">
                            <img src="/wp-content/uploads/2025/03/icon-order.jpg" alt="" data-lazy-src="/wp-content/uploads/2025/03/icon-order.jpg" data-ll-status="loaded" class="entered lazyloaded">
                        </div>
                        <div class="info">
                            <a href="todo">
                                <h3 class="title">Đổi tiền Alipay </h3>
                            </a>

                            <div class="desc">
                                <p>Hỗ trợ quý khách hàng nạp tiền vào tài khoản Alipay nhanh chóng, an toàn. Đổi tiền Trung – Việt tỷ giá thấp.</p>
                                <ul>
                                    <li>Nạp tiền nhanh chóng, tiện lợi</li>
                                    <li>Phí dịch vụ thấp nhất thị trường</li>
                                    <li>Tỷ giá thấp</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}
add_shortcode('group-order', 'mytheme_group_order_shortcode');
