<?php
if (is_page('don-hang')) {
    include_once get_template_directory() . '/mua-hang/don-hang.php';
} else if (is_page('mua-hang')) {
    include_once get_template_directory() . '/mua-hang/dashboard.php';
} else if (is_page('wallet')) {
    include_once get_template_directory() . '/mua-hang/vi-dien-tu.php';
} else if (is_page('khieu-nai')) {
    include_once get_template_directory() . '/mua-hang/khieu-nai.php';
} else if (is_page('gio-hang')) {
    include_once get_template_directory() . '/mua-hang/gio-hang.php';
} else if (is_page('chi-tiet-don-hang')) {
    include_once get_template_directory() . '/mua-hang/chi-tiet-don-hang.php';
} else if (is_page('don-hang-ky-gui')) {
    include_once get_template_directory() . '/mua-hang/don-hang-ki-gui.php';
} else if (is_page('nap-tien')) {
    include_once get_template_directory() . '/mua-hang/nap-tien.php';
} else if (is_page('tai-khoan')) {
    include_once get_template_directory() . '/mua-hang/tai-khoan.php';
} else if (is_page('doi-mat-khau')) {
    include_once get_template_directory() . '/mua-hang/doi-mat-khau.php';
}
