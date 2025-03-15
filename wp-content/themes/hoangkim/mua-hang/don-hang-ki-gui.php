<?php
$mockData = [
    []
];
global $wpdb;
$user_id = get_current_user_id();
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}orders WHERE user_id = %d AND type = 1 ORDER BY created_at DESC",
    $user_id
);
$orders = $wpdb->get_results($query);
$status_str = ["", "NCC phát hàng", 'Nhập kho TQ', 'TQ gửi hàng', 'Nhập kho VN', 'Khách nhận hàng', 'Không rõ nguồn gốc'];

?>

<div class="dashboard">
    <div class="mt-3 flex-1 don-hang-ky-gui">
        <h4 class="text-uppercase">Đơn hàng ký gửi</h4>
        <div class="alert alert-warning alert-deafault btn-flat">
            <p class="notification"></p>
            <p>
                <em><u><strong>Chú ý:</strong></u></em><br>
                - Để tạo đơn hàng ký gửi, quý khách vui lòng điền đầy đủ thông tin về mặt hàng của quý khách theo đúng định dạng của chúng tôi. Xin cám ơn!.
            </p>
            <p></p>
        </div>
        <!-- <div class="notification-dashboard">
            <div class="mt-3">

            </div>
        </div> -->
        <form id="frm_action" class="form-horizontal" name="frm_action" method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="txtcustomer" id="txtcustomer" value="lsang2885@gmail.com">
            <input type="hidden" name="str_bao_hiem" class="str_bao_hiem">
            <input type="hidden" name="str_kiem_dem" class="str_kiem_dem">
            <input type="hidden" name="str_gia_co" class="str_gia_co">
            <div class="box_data table-responsive">
                <table class="table table-bordered tbl_add_orderext" style="min-width: 1000px;">
                    <thead>
                        <tr class="hidden-xs">
                            <th>
                                <a href="#" class="btn btn-success add_item_order" onclick="addItem(this)"><i class="fa fa-plus"></i></a>
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
                <form name="frm_search_logistic" class="frm_search_logistic" method="post">
                    <div class="box-filter">
                        <div class="row">
                            <div class="col-lg-3 col-xs-12 marbot15">
                                <div class="">
                                    <input name="search_logistic_code" type="text" class="form-control search_logistic_code" placeholder="Tìm theo mã vận đơn" value="">
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-12 marbot15">
                                <div class="">
                                    <select name="sl_status_logistic" id="sl_status_logistic" class="form-control sl_status_logistic">
                                        <option value="">--Chọn trạng thái--</option>
                                        <option value="1">NCC phát hàng</option>
                                        <option value="2">Nhập kho TQ</option>
                                        <option value="3">TQ gửi hàng</option>
                                        <option value="4">Nhập kho VN</option>
                                        <option value="6">Khách nhận hàng</option>
                                        <option value="7">Không rõ nguồn gốc</option>
                                    </select>
                                    <!-- <script type="text/javascript">
                                        cbo_Selected("sl_status_logistic", "");
                                    </script> -->
                                </div>
                            </div>
                            <div class="col-md-3 marbot15">
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="date" id="txtdatefrom" name="txtdatefrom" class="form-control txtdatefrom" placeholder="Từ ngày" value="">
                                </div>
                            </div>
                            <div class="col-md-3 marbot15">
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="date" id="txtdateto" name="txtdateto" class="form-control txtdateto" placeholder="Đến ngày" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div>
                    <div class="box_data table-responsive">
                        <table class="table table-bordered" style="min-width: 1000px;">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Order Code</th>
                                    <th>Vận đơn</th>
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
                                            <a href="chi-tiet-don-hang/?id=<?php echo $order->id ?>"><?php echo "HK_" . $order->id ?></a>
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
                    <!-- <div class="text-left">
					<a href="#" class="btn btn-danger disabled btn_delete_logistic"><i class="fa fa-trash"></i> <span style="text-transform: uppercase;">Xóa vận đơn </span></a>
				</div> -->
                    <!-- phan trang -->
                    <p align="center" class="paging"><strong>Total:</strong> 0 <strong>on</strong> 0 <strong>page</strong><br></p>
                    <!--end phan trang -->
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

    $(document).ready(function() {
        $('.save_order').on('click', function() {
            const data = [];
            $('.tbl_add_orderext tbody tr').each((_, tr) => {
                data.push({
                    van_don: $(tr.querySelector('.txt_logistic_code')).val(),
                    thuong_hieu: $(tr.querySelector('.txt_name_vn')).val(),
                    brand: $(tr.querySelector('.txt_brand')).val(),
                    so_kien_hang: $(tr.querySelector('.txt_quantity')).val(),
                    note: $(tr.querySelector('.txt_note')).val(),
                })
            })
            const error = data.some(e => !e.van_don || !e.thuong_hieu || !e.brand || !e.so_kien_hang);
            if (error) return alert("Vui lòng nhập đầy đủ thông tin")
            data.forEach(d => {
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'create_order_ki_gui',
                        nonce: '<?php echo wp_create_nonce('create_order_nonce'); ?>',
                        ...d
                    },
                    success: function(response) {
                        alert(response.data.message);
                        window.location.reload()
                    },
                    error: function() {
                        alert('Lỗi kết nối đến máy chủ.');
                    }
                });
            })
        })
    })
</script>