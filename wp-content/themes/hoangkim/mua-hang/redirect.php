<?php
if (is_page('don-hang')) {
    include_once get_template_directory() . '/mua-hang/don-hang.php';
} else if (is_page('mua-hang')) {
    include_once get_template_directory() . '/mua-hang/dashboard.php';
} else if (is_page('wallet')) {
    include_once get_template_directory() . '/mua-hang/vi-dien-tu.php';
} else if (is_page('khieu-nai')) {
    include_once get_template_directory() . '/mua-hang/khieu-nai.php';
}
