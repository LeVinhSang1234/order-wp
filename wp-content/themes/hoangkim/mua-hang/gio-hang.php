<?php
global $wpdb;
$table_name = $wpdb->prefix . 'cart';
$user_id = get_current_user_id();
$cart_items = $wpdb->get_results(
  $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND is_done = 0 ORDER BY added_at DESC", $user_id),
  ARRAY_A
);
$grouped_cart = [];
foreach ($cart_items as $item) {
  $grouped_cart[$item['shop_id']][] = $item;
}
$rate = floatval(get_option('exchange_rate', 1.0));
$phi_mua_hang = floatval(get_option('phi_mua_hang', 1.0));
$current_user = wp_get_current_user();
?>

<div class="dashboard">
  <div class="mt-3 flex-1">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <h4 class="text-uppercase">Giỏ hàng</h4>
      <button class="btn-order-all flex justify-content-center" style="width: 200px;"
        data-item="<?php echo $product['id'] ?>">
        <i class="fa-solid fa-cart-plus"></i>
        Yêu cầu tất cả
      </button>
    </div>
    <div class="notification-dashboard">
      <div class="mt-3">
        <?php if (count($cart_items) <= 0) { ?>
          <div class="text-uppercase" style="font-size: 13px">Bạn chưa có sản phẩm nào trong giỏ hàng</div>
        <?php } ?>
        <?php foreach ($grouped_cart as $shop_id => $products) {
          $shop_url = $products[0]['shop_url'];
          $totalPrice = 0;
          $allSelected = true;
          foreach ($products as $product) {
            if ($product['is_select'] != 1) {
              $allSelected = false;
              break;
            }
          }
        ?>
          <div class="group-cart">
            <div class="cart-header">
              <a class="d-flex align-items-center gap-1" target="_blank" href=" <?php echo $shop_url ?>">
                <input <?php echo ($allSelected ? "checked" : "") ?> data-type="select-carts" type="checkbox"
                  data-item="<?php echo $shop_id ?>" />
                <?php echo $shop_id ?>
              </a>
              <div class="d-flex align-items-center cart-option gap-3">
                <div class="d-flex align-items-center gap-1">
                  <input data-shop="<?php echo $shop_id ?>" data-type="gia-co-dong-go" type="checkbox" />
                  <span>Gia cố, đóng gỗ</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <input data-shop="<?php echo $shop_id ?>" data-type="kiem-dem-hang" type="checkbox" />
                  <span>Kiểm đếm hàng</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <input data-shop="<?php echo $shop_id ?>" data-type="bao-hiem" type="checkbox" />
                  <span>Bảo hiểm hàng hoá</span>
                </div>
              </div>
            </div>
            <div data-shop="<?php echo $shop_id ?>"
              class="table-wrap d-flex gap-3 mb-3 align-items-baseline table-responsive">
              <table data-shop="<?php echo $shop_id ?>" class="table-cart flex-2 mt-1" style="min-width: 1000px;">
                <thead>
                  <th class="text-center" style="width: 50px"></th>
                  <th class="text-center" style="width: 100px">Hình ảnh</th>
                  <th class="text-center" style="width: 250px">Size - Màu sắc</th>
                  <th>Ghi chú</th>
                  <th>Đơn giá tạm tính</th>
                  <th>Số lượng</th>
                  <th class="text-center" style="width: 80px">Xóa</th>
                </thead>
                <tbody>
                  <?php foreach ($products as $product) {
                    $totalPrice += $product['quantity'] * $product['price'];
                  ?>
                    <tr>
                      <td class="text-center">
                        <input <?php echo ($product['is_select'] ? "checked" : "") ?>
                          data-item="<?php echo $product['id'] ?>" data-shop="<?php echo $shop_id ?>"
                          data-type="select-cart" type="checkbox" />
                      </td>
                      <td class="text-center"><img src="<?php echo $product['product_image'] ?>" /></td>
                      <td class="text-center"><?php echo $product['size'] ?> <br><?php echo $product['color'] ?></td>
                      <td>
                        <textarea readonly class="w-100"><?php echo $product['product_note'] ?></textarea>
                      </td>
                      <td data-type="price" data-item="<?php echo $product['price'] * $rate ?>"
                        data-id="<?php echo $product['id'] ?>">
                        <?php echo format_price_vnd($product['price'] * $rate) ?>
                      </td>
                      <td>
                        <input data-item="<?php echo $product['id'] ?>" data-shop="<?php echo $shop_id ?>"
                          data-type="product-quantity" value="<?php echo $product['quantity'] ?>" />
                      </td>
                      <td class="text-center">
                        <div class="icon-remove" data-item="<?php echo $product['id'] ?>">
                          <i class="fa-regular fa-trash-can"></i>
                        </div>
                      </td>
                    </tr>
                  <?php }
                  $totalPrice = $totalPrice * $rate;
                  $phiMua = $totalPrice * $phi_mua_hang;
                  ?>
                </tbody>
              </table>
              <div class="flex-1 total-order">
                <ul>
                  <li>
                    Tiền hàng<strong data-type="total-money-product"><?php echo format_price_vnd($totalPrice) ?></strong>
                  </li>
                  <li>
                    Phí mua hàng (<?php echo $phi_mua_hang; ?>%)<strong data-type="phi-mua-product">
                      <?php echo format_price_vnd($phiMua) ?>
                    </strong>
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
                    TỔNG TIỀN TẠM TÍNH<strong
                      data-type="total-product"><?php echo format_price_vnd($totalPrice + $phiMua) ?></strong>
                  </li>
                </ul>
                <div class="mt-2 mb-1" style="font-size: 12px">Ghi chú đơn hàng</div>
                <textarea data-shop="<?php echo $shop_id ?>" data-type="note-product" style="font-size: 13px;"
                  class="w-100" placeholder="Ghi chú đơn hàng này"></textarea>
                <div class="w-100 d-flex justify-content-end">
                  <button data-shop="<?php echo $shop_id ?>" class="mt-2 btn-order flex justify-content-center"
                    style="width: 200px;" data-item="<?php echo $product['id'] ?>">
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
<div class="popup-info">
  <div class="popup-content">
    <h3 class="text-center">Thông tin giao hàng</h3>
    <div class="content-data">
      <div class="mt-3 address-info">
        <div>
          <p>Họ tên: <span class="mess-error">*</span></p>
          <input data-type="ho_ten" value="<?php echo $current_user->display_name ?>" />
          <span class="mess-error" item-type="name-error">Vui lòng nhập tên</span>
        </div>
        <div>
          <p>Địa chỉ: </p>
          <input data-type="address" value="<?php echo display_user_address(); ?>" />
          <span class="mess-error" item-type="address-error">Vui lòng nhập địa chỉ</span>
        </div>
        <div>
          <p>Email: <span class="mess-error">*</span></p>
          <input data-type="email" value="<?php echo $current_user->user_email; ?>" />
          <span class="mess-error" item-type="email-error">Vui lòng nhập email</span>
        </div>
        <div>
          <p>Số điện thoại: <span class="mess-error">*</span></p>
          <input data-type="phone" value="<?php echo display_user_phone(); ?>" />
          <span class="mess-error" item-type="phone-error">Vui lòng nhập số điện thoại</span>
        </div>
        <div class="mt-4 w-100 d-flex justify-content-center gap-2">
          <button class="mt-2 btn-cancel btn-cancel-popup-order">
            Cancel
          </button>
          <button class="mt-2 btn-accept-order">
            <i class="fa-solid fa-cart-plus"></i>
            Đặt hàng
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    function formatCurrencyVND(amount) {
      return amount ? new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND"
      }).format(amount) : '--';
    }

    $('.table-cart').on('click', '.icon-remove', function() {
      if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) return;
      fetch(`${origin}/wp-admin/admin-ajax.php?action=remove_cart`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            id: $(this).data('item')
          }),
          credentials: "include",
        })
        .finally(() => window.location.reload());
    });

    $('input[data-type="select-carts"]').on("click", function() {
      $(`input[data-shop="${$(this).data('item')}"]`).click();
    });

    $('input[data-type="select-cart"], input[data-type="product-quantity"]').on("change", function() {
      const shopId = $(this).data('shop');
      const productId = $(this).data('item');
      const isSelect = $('input[data-shop="' + shopId + '"][data-item="' + productId + '"][data-type="select-cart"]').is(":checked");
      const quantity = parseInt($(this).val()) || 1;

      $(this).val(quantity);

      fetch(`${origin}/wp-admin/admin-ajax.php?action=update_cart_item`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            cart_id: productId,
            quantity,
            is_select: Number(isSelect)
          })
        })
        .finally(() => {
          window.location.reload()
        });
    });

    $('button.btn-order').on('click', function() {
      const shopId = $(this).data('shop');
      if (!$(`input[data-type="select-cart"][data-shop="${shopId}"]:checked`).length) return alert(
        "Vui lòng chọn sản phẩm sẽ mua!");

      const data = {
        action: 'create_order',
        nonce: '<?php echo wp_create_nonce('create_order_nonce'); ?>',
        shop_id: shopId,
        ho_ten: $('.popup-info input[data-type="ho_ten"]').val(),
        address: $('.popup-info input[data-type="address"]').val(),
        email: $('.popup-info input[data-type="email"]').val(),
        phone: $('.popup-info input[data-type="phone"]').val(),
        note: $('.textarea[data-type="note-product"]').val(),
        is_gia_co: Number($(`input[data-shop="${shopId}"][data-type="gia-co-dong-go"]`).is(":checked")),
        is_kiem_dem_hang: Number($(`input[data-shop="${shopId}"][data-type="kiem-dem-hang"]`).is(":checked")),
        is_bao_hiem: Number($(`input[data-shop="${shopId}"][data-type="bao-hiem"]`).is(":checked"))
      };

      $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response) {
        alert(response.data.message);
        window.location.reload();
      }).fail(() => alert('Lỗi kết nối đến máy chủ.'));
    });

    $('.btn-order-all').on('click', function() {
      $('input[data-type="select-cart"]').prop("checked", true).trigger("change");
      let productsByShop = {};

      $('input[data-type="select-cart"]:checked').each(function() {
        const shopId = $(this).data('shop');
        const productId = $(this).attr('data-item');
        const quantity = $(`input[data-type="product-quantity"][data-item="${productId}"]`).val();

        if (!productsByShop[shopId]) {
          productsByShop[shopId] = [];
        }

        productsByShop[shopId].push({
          id: productId,
          quantity: parseInt(quantity, 10) || 1
        });
      });

      for (const shopId in productsByShop) {
        fetch(`${origin}/wp-admin/admin-ajax.php?action=submit_all_cart`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              ho_ten: $(`input[data-type="ho_ten"]`).val(),
              address: $(`input[data-type="address"]`).val(),
              email: $(`input[data-type="email"]`).val(),
              phone: $(`input[data-type="phone"]`).val(),
              shop_id: shopId,
              products: productsByShop[shopId]
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert("Đã gửi yêu cầu đặt hàng thành công!");
              location.reload();
            } else {
              alert("Có lỗi xảy ra, vui lòng thử lại.");
            }
          });
      }
    });
  })
</script>
