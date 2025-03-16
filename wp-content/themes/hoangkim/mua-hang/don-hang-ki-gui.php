<?php
$mockData = [
    [
        "test" => 1
    ]
];
$mockDetailData = [
    [
        "test" => 1
    ]
];
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
            <div class="box_data">
                <table class="table table-bordered tbl_add_orderext">
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
                    <tbody id='tbody-create-order'>
                    </tbody>
                </table>
            </div>
            <div class="clear-both text-left">
                <a class="btn btn-primary save_order" style="color: #fff;" name="cmdsave" id="cmdsave" onclick="return check_input();">Lưu đơn hàng</a>
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
                                    <script type="text/javascript">
                                        cbo_Selected("sl_status_logistic", "");
                                    </script>
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

                            <div class="col-lg-4 col-xs-12 marbot15">
                                <div class="">
                                    <button type="submit" class="btn btn-primary btn_search_mvd" value="search_mvd">TÌM KIẾM</button>
                                    <a href="#" class="btn btn-success btn_xuat_excel_mvd">XUẤT EXCEL</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div>
                    <div class="box_data">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="chk_all_order" class="chk_all_order">
                                    </th>
                                    <th>Ngày</th>
                                    <th>Order Code</th>
                                    <th>Vận đơn</th>
                                    <th>Tên hàng hóa</th>
                                    <th>Thương hiệu</th>
                                    <th>Số kiện</th>

                                    <th class="hidden">Kiểm đếm</th>
                                    <th class="hidden">Đóng gỗ</th>
                                    <th class="hidden">Bảo hiểm</th>
                                    <th>Kg tính phí</th>
                                    <th>Giá phí</th>
                                    <th>Thành tiền</th>
                                    <th>Tình trạng</th>
                                    <th>Lưu ý</th>
                                </tr>
                            </thead>
                            <tbody id='table-detail-order'>

                            </tbody>
                        </table>
                    </div>
                    <p align="center" class="paging"><strong>Total:</strong> 0 <strong>on</strong> 0 <strong>page</strong><br></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var mockData = <?php echo json_encode($mockData); ?>;
    var mockDetailData = <?php echo json_encode($mockDetailData); ?>;

    function renderTableCreateOrder() {
        var tbody = document.querySelector('#tbody-create-order');
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

    function renderTableDetailOrder() {
        var tbody = document.querySelector('#table-detail-order');
        tbody.innerHTML = "";
        mockDetailData.forEach((item, index) => {
            var row = `<tr class="row_logistic_17260">
                                    <td>
                                        <input type="checkbox" name="chk_select_order[]" class=" chk_select_order" value="17260">
                                    </td>
                                    <td>
                                        09:46<br>
                                        13/03/2025 </td>
                                    <td>
                                        <a href="https://hungthinh36.com/don-hang-extension/chi-tiet/16864" target="_blank">
                                            KG_16864 </a>
                                    </td>
                                    <td>1 </td>
                                    <td> 1 </td>
                                    <td> 1 </td>
                                    <td align="center"> 1 </td>
                                    <td align="center" class="hidden"> <i class="fa fa-remove" style="color:red;"></i>
                                    </td>
                                    <td align="center" class="hidden"> <i class="fa fa-remove" style="color:red;"></i> </td>
                                    <td align="center" class="hidden"> <i class="fa fa-remove" style="color:red;"></i> </td>
                                    <td align="center"> 0.0 kg </td>
                                    <td align="center"> ...
                                    </td>
                                    <td align="center"> ... </td>
                                    <td align="center"> NCC phát hàng </td>
                                    <td> 1 </td>
                                </tr>`;

            tbody.innerHTML += row;
        });
    }

    function deleteItem(index) {
        mockData.splice(index, 1)
        renderTableCreateOrder();
    }

    function addItem() {
        mockData.push({
            test: 1
        })
        renderTableCreateOrder();
    }
    renderTableCreateOrder()
    renderTableDetailOrder()
</script>