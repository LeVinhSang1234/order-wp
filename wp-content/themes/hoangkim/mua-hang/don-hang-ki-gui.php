<?php
$mockData = [
    []
];
global $wpdb;
$user_id = get_current_user_id();
$time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$van_don = isset($_GET['van_don']) ? sanitize_text_field($_GET['van_don']) : '';

$query = "SELECT * FROM {$wpdb->prefix}orders WHERE user_id = %d";
$params = [$user_id];


if (!empty($time_from)) {
    $query .= " AND created_at >= %s";
    $params[] = $time_from;
}

if (!empty($time_to)) {
    $query .= " AND created_at <= %s";
    $params[] = $time_to;
}

if (!empty($type)) {
    $query .= " AND type = %s";
    $params[] = $type;
}

if (!empty($van_don)) {
    $query .= " AND van_don LIKE %s";
    $params[] = '%' . $wpdb->esc_like($van_don) . '%';
}

$query .= " AND type = 1 ORDER BY created_at ASC, created_at DESC ";

$orders = $wpdb->get_results($wpdb->prepare($query, ...$params));

$status_str = ["", "Chờ báo giá", "Đang mua hàng", "Đã mua hàng", "NCC phát hàng", "Nhập kho TQ", "Nhập kho VN", "Khách nhận hàng", "Đơn hàng hủy", "Đơn khiếu nại"];

?>

<div class="dashboard">
    <div class="mt-3 flex-1 don-hang-ky-gui">
        <h4 class="text-uppercase">Đơn hàng ký gửi</h4>
        <div class="alert alert-warning alert-deafault btn-flat">
            <p class="notification"></p>
            <p>
                <em><u><strong>Chú ý:</strong></u></em><br>
                - Để tạo đơn hàng ký gửi, quý khách vui lòng điền đầy đủ thông tin về mặt hàng của quý khách theo đúng
                định dạng của chúng tôi. Xin cám ơn!.
            </p>
            <p></p>
        </div>
        <form id="frm_action" class="form-horizontal" name="frm_action" method="post" action=""
            enctype="multipart/form-data">
            <input type="hidden" name="txtcustomer" id="txtcustomer" value="lsang2885@gmail.com">
            <input type="hidden" name="str_bao_hiem" class="str_bao_hiem">
            <input type="hidden" name="str_kiem_dem" class="str_kiem_dem">
            <input type="hidden" name="str_gia_co" class="str_gia_co">
            <div class="box_data table-responsive">
                <table class="w-100 mt-2 tbl_add_orderext" style="min-width: 1000px;">
                    <thead>
                        <tr class="hidden-xs">
                            <th>
                                <a href="#" class="btn btn-success add_item_order" onclick="addItem(this)"><i
                                        class="fa fa-plus"></i></a>
                            </th>
                            <th>Mã vận đơn<font color="red">*</font>
                            </th>

                            <th>Tên hàng hóa<font color="red">*</font>
                            </th>
                            <th>Thương hiệu<font color="red">*</font>
                            </th>
                            <th>Số kiện<font color="red">*</font>
                            </th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="clear-both text-left mt-3">
                <a class="btn btn-primary save_order" style="color: #fff;" name="cmdsave" id="cmdsave">Lưu đơn hàng</a>
            </div>
        </form>
        <hr>
        <div class="row">
            <div class="col-md-12 box_list_logistic" style="margin-top: 35px;">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input id="van_don" class="w-filter-full" placeholder="Tìm theo mã vận đơn" />
                    <?php
                    $id = "time_from";
                    $placeholder = "Từ ngày";
                    include get_template_directory() . '/mua-hang/input-date-picker.php';
                    ?>
                    <?php
                    $id = "time_to";
                    $placeholder = "Đến ngày";
                    include get_template_directory() . '/mua-hang/input-date-picker.php';
                    ?>
                    <select name="status" class="w-filter-full" id="type">
                        <option value="">--Chọn trạng thái--</option>
                        <option value="4">NCC phát hàng</option>
                        <option value="5">Nhập kho TQ</option>
                        <option value="6">Nhập kho VN</option>
                        <option value="7">Khách nhận hàng</option>
                        <option value="8">Đơn hàng hủy</option>
                        <option value="9">Đơn khiếu nại</option>
                    </select>
                    <button class="btn-find"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div class="mt-3">
                    <div class="box_data table-responsive">
                        <table class="w-100 mt-2" style="min-width: 1000px;">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Order Code</th>
                                    <th>Mã vận đơn</th>
                                    <th>Tên hàng hóa</th>
                                    <th>Thương hiệu</th>
                                    <th>Số kiện</th>
                                    <th>Kg tính phí</th>
                                    <th>Giá phí</th>
                                    <th>Thành tiền</th>
                                    <th>Tình trạng</th>
                                    <th>Lưu ý</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-data">
                                <?php foreach ($orders as $order) {
                                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $date->format('d/m/Y H:i') ?>
                                        </td>
                                        <td>
                                            <a
                                                href="chi-tiet-don-hang/?id=<?php echo $order->id ?>"><?php echo "HK_" . $order->id ?></a>
                                        </td>
                                        <td><?php echo $order->van_don ?></td>
                                        <td><?php echo $order->brand ?></td>
                                        <td><?php echo $order->thuong_hieu ?></td>
                                        <td><?php echo $order->so_kien_hang ?></td>
                                        <td>--</td>
                                        <td>--</td>
                                        <td>--</td>
                                        <td><?php echo $status_str[$order->status] ?></td>
                                        <td><?php echo $order->note ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <p align="center" class="paging">
                        <strong>Total:</strong><?php echo count($orders) ?><strong>on</strong>
                        <?php
                        $result = count($orders) / 10;
                        $rounded_up = ceil($result);
                        echo $rounded_up;
                        ?>
                        <strong>page</strong><br>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var mockData = <?php echo json_encode($mockData); ?>;

    function renderTable() {

        var tbody = document.querySelector('tbody');
        tbody.innerHTML = "";

        mockData.forEach((item, index) => {
            var row = `<tr>
            <td align="left" class="cls_td">
                <a href="#" class="btn_action btn_del" onclick="deleteItem(${index});return false;">
                    <i class="fa fa-trash fa_del"></i>
                </a>
            </td>
            <td><input name="txt_logistic_code[]" class="form-control txt_logistic_code"
                type="text" placeholder="Mã vận đơn" required=""></td>
            <td><input name="txt_name_vn[]" class="form-control txt_name_vn" type="text" placeholder="Tên hàng hóa..."></td>
            <td><input name="txt_brand[]" class="form-control txt_brand" type="text" placeholder="Thương hiệu..."></td>
            <td><input name="txt_quantity[]" min="1" class="form-control txt_quantity" type="number" value="1" placeholder="SL"></td>
            <td class="cls_td"><input name="txt_note[]" class="form-control txt_note" placeholder="Loại hàng, Kích thước..."></td>
        </tr>`;

            tbody.innerHTML += row;
        });
    }

    function deleteItem(index) {
        mockData.splice(index, 1)
        renderTable();
    }

    function addItem() {
        mockData.push({
            test: 1
        })
        renderTable();
    }
    renderTable()

    $(document).ready(function () {

        // create order
        $('.save_order').on('click', function () {
            const data = [];
            $('.tbl_add_orderext tbody tr').each((_, tr) => {
                data.push({
                    van_don: $(tr.querySelector('.txt_logistic_code')).val(),
                    thuong_hieu: $(tr.querySelector('.txt_name_vn')).val(),
                    brand: $(tr.querySelector('.txt_brand')).val(),
                    so_kien_hang: $(tr.querySelector('.txt_quantity')).val(),
                    note: $(tr.querySelector('.txt_note')).val(),
                });
            });
            const error = data.some(e => !e.van_don || !e.thuong_hieu || !e.brand || !e.so_kien_hang);
            if (error) return alert("Vui lòng nhập đầy đủ thông tin");

            if (!confirm("hoặc là em để Bạn cần xác nhận lại thông tin đơn hàng 1 lần nữa trước khi lưu chính xác nhé.")) return;

            data.forEach(d => {
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'create_order_ki_gui',
                        nonce: '<?php echo wp_create_nonce('create_order_nonce'); ?>',
                        ...d
                    },
                    success: function (response) {
                        alert(response.data.message);
                        window.location.reload();
                    },
                    error: function () {
                        alert('Lỗi kết nối đến máy chủ.');
                    }
                });
            });
        })

        const params = new URLSearchParams(window.location.search);
        if (params.has('time_from')) $('#time_from').val(params.get('time_from').replace(/\//g, '-'));
        if (params.has('time_to')) $('#time_to').val(params.get('time_to').replace(/\//g, '-'));
        if (params.has('type')) $('#type').val(params.get('type'));
        if (params.has('van_don')) $('#van_don').val(params.get('van_don'));

        $('.btn-find').on('click', function (event) {
            event.stopPropagation();

            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                if (isNaN(date)) return '';
                return date.getFullYear() + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0');
            };

            const time_from = formatDate($('#time_from').val());
            const time_to = formatDate($('#time_to').val());
            const type = $('#type').val();
            const van_don = $('#van_don').val();
            let url = new URL(window.location.href);
            let params = url.searchParams;

            if (time_from) params.set('time_from', time_from);
            else params.delete('time_from');

            if (time_to) params.set('time_to', time_to);
            else params.delete('time_to');

            if (type) params.set('type', type);
            else params.delete('type');

            if (van_don) params.set('van_don', van_don);
            else params.delete('van_don');

            window.history.pushState({}, '', url.pathname + '?' + params.toString());
            window.location.reload();
        });
    })
</script>