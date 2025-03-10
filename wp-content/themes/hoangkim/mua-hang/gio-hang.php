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
function format_price_vnd($price)
{
    return number_format($price, 0, ',', '.') . ' ₫';
}
$rate = floatval(get_option('exchange_rate', 1.0));
$phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));
?>

<div class="dashboard">
    <div class="mt-3 flex-1">
        <h4 class="text-uppercase">Giỏ hàng</h4>
        <div class="notification-dashboard">
            <div class="mt-3">
                <?php if (count($cart_items) <= 0) { ?>
                    <div class="text-uppercase" style="font-size: 13px">Bạn chưa có sản phẩm nào trong giỏ hàng</div>
                <?php } ?>
                <?php foreach ($grouped_cart as $shop_id => $products) {
                    $shop_url = $products[0]['shop_url'];
                ?>
                    <div class="group-cart">
                        <div class="cart-header">
                            <a class="d-flex align-items-center gap-1" target="_blank" href=" <?php echo $shop_url ?>">
                                <input data-type="select-carts" type="checkbox" data-item="<?php echo $shop_id ?>" />
                                <?php echo $shop_id ?>
                            </a>
                            <div class="d-flex align-items-center cart-option gap-3">
                                <div class="d-flex align-items-center gap-1">
                                    <input data-item="" type="checkbox" />
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
                        <div data-shop="<?php echo $shop_id ?>" class="table-wrap d-flex gap-3 mb-3 align-items-baseline">
                            <table data-shop="<?php echo $shop_id ?>" class="table-cart flex-2 mt-1">
                                <thead>
                                    <th class="text-center" style="width: 50px"></th>
                                    <th class="text-center" style="width: 100px">Hình ảnh</th>
                                    <th>Ghi chú</th>
                                    <th>Đơn giá tạm tính</th>
                                    <th>Số lượng</th>
                                    <th class="text-center" style="width: 80px">Xóa</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product) { ?>
                                        <tr>
                                            <td class="text-center">
                                                <input data-item="<?php echo $product['id'] ?>" data-shop="<?php echo $shop_id ?>" data-type="select-cart" type="checkbox" />
                                            </td>
                                            <td class="text-center"><img src="<?php echo $product['product_image'] ?>" /></td>
                                            <td>
                                                <textarea readonly class="w-100"><?php echo $product['product_note'] ?></textarea>
                                            </td>
                                            <td data-type="price" data-item="<?php echo $product['price'] * $rate ?>" data-id="<?php echo $product['id'] ?>">
                                                <?php echo format_price_vnd($product['price'] * $rate) ?>
                                            </td>
                                            <td>
                                                <input data-item="<?php echo $product['id'] ?>" data-shop="<?php echo $shop_id ?>" data-type="product-quantity" value="<?php echo $product['quantity'] ?>" />
                                            </td>
                                            <td class="text-center">
                                                <div class="icon-remove" data-item="<?php echo $product['id'] ?>">
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
                                        Tiền hàng<strong data-type="total-money-product">--</strong>
                                    </li>
                                    <li>
                                        Phí mua hàng (<?php echo $phi_mua_hang; ?>%)<strong data-type="phi-mua-product">--</strong>
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
                                        TỔNG TIỀN TẠM TÍNH<strong data-type="total-product">--</strong>
                                    </li>
                                </ul>
                                <div class="mt-2 mb-1" style="font-size: 12px">Ghi chú đơn hàng</div>
                                <textarea data-shop="<?php echo $shop_id ?>" data-type="note-product" style="font-size: 13px;" class="w-100" placeholder="Ghi chú đơn hàng này"></textarea>
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
<script>
    $(document).ready(function() {

        function formatCurrencyVND(amount) {
            if (!amount) return '--'
            return new Intl.NumberFormat("vi-VN", {
                style: "currency",
                currency: "VND",
            }).format(amount);
        }

        $('.table-cart .icon-remove').on('click', function() {
            const id = $(this).attr('data-item');
            const userConfirmed = confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?");
            if (!userConfirmed) return
            return fetch(`${origin}/wp-admin/admin-ajax.php?action=remove_cart`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id
                    }),
                    credentials: "include",
                })
                .catch(() => null)
                .finally(() => window.location.reload());
        })

        $('input[data-type="select-carts"]').on("click", function() {
            // const val = $(this).is(":checked")
            // const shopId = $(this).attr('data-item')
            // $(`input[data-shop="${shopId}"]`).prop('checked', val);
            // calPrice(shopId)
        })

        $('input[data-type="select-cart"]').on("click", function() {
            fetch(`${origin}/wp-admin/admin-ajax.php?action=update_cart_item`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id
                    }),
                    credentials: "include",
                })
                .catch(() => null)
                .finally(() => window.location.reload());
        })

        $('input[data-type="product-quantity"]').on('change', function() {
            const val = $(this).val();
            $(this).val(Number(val) || 1)
            const shopId = $(this).attr("data-shop")
            calPrice(shopId)
        })
    })
</script>