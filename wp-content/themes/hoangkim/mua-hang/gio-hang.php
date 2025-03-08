<?php
global $wpdb;
$table_name = $wpdb->prefix . 'cart';
$user_id = get_current_user_id();
$cart_items = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d ORDER BY added_at DESC", $user_id),
    ARRAY_A
);
$grouped_cart = [];
foreach ($cart_items as $item) {
    $grouped_cart[$item['shop_id']][] = $item;
}

?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Giỏ hàng</h4>
        <div class="notification-dashboard">
            <div class="mt-3">
                <?php foreach ($grouped_cart as $shop_id => $products) {
                    $shop_url = $products[0]['shop_url'];
                ?>
                    <div class="group-cart">
                        <div class="cart-header">
                            <a class="d-flex align-items-center gap-1" target="_blank" href=" <?php echo $shop_url ?>">
                                <input type="checkbox" />
                                <?php echo $shop_id ?>
                            </a>
                            <div class="d-flex align-items-center cart-option gap-3">
                                <div class="d-flex align-items-center gap-1">
                                    <input type="checkbox" />
                                    <span>Gia cố, đóng gỗ</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="checkbox" />
                                    <span>Kiểm đếm hàng</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="checkbox" />
                                    <span>Bảo hiểm hàng hoá</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-baseline">
                            <table class="table-cart flex-2 mt-1">
                                <thead>
                                    <th class="text-center" style="width: 50px"></th>
                                    <th class="text-center" style="width: 100px">Hình ảnh</th>
                                    <th>Ghi chú</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-center" style="width: 80px">Xóa</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product) { ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" />
                                            </td>
                                            <td class="text-center"><img src="<?php echo $product['product_image'] ?>" /></td>
                                            <td>
                                                <textarea class="w-100"><?php echo $product['product_note'] ?></textarea>
                                            </td>
                                            <td>
                                                ￥<?php echo $product['price'] ?>
                                            </td>
                                            <td>
                                                <input value="<?php echo $product['quantity'] ?>" />
                                            </td>
                                            <td class="text-center">
                                                <div class="icon-remove">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="flex-1 total-order">
                                <ul>
                                    <li>
                                        Tiền hàng<strong>0</strong>
                                    </li>
                                    <li>
                                        Phí mua hàng (0.0%)<strong>0đ</strong>
                                    </li>
                                    <li>
                                        Phí bảo hiểm <strong>--</strong>
                                    </li>
                                    <li>
                                        Phí kiểm đếm <strong>--</strong>
                                    </li>
                                    <li>
                                        Phí đóng kiện gỗ <strong>--</strong>
                                    </li>
                                    <li class="text-uppercase">
                                        TỔNG TIỀN <strong>--</strong>
                                    </li>
                                </ul>
                                <div class="mt-2 mb-1" style="font-size: 12px">Ghi chú đơn hàng</div>
                                <textarea style="font-size: 13px;" class="w-100" placeholder="Ghi chú đơn hàng này"></textarea>
                                <div class="w-100 d-flex justify-content-end">
                                    <button class="mt-2 btn-order">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Yêu cầu báo giá
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>