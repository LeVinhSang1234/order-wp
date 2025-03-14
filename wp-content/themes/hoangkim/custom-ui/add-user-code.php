<?php

// Tự động thêm mã code
function custom_generate_user_code($user_id)
{
    $code = 'MS-' . $user_id;
    update_user_meta($user_id, 'user_code', $code);
}
add_action('user_register', 'custom_generate_user_code');

// Hiển thị code trong màn list
function custom_add_user_code_column($columns)
{
    $columns['user_code'] = 'Code';
    return $columns;
}
add_filter('manage_users_columns', 'custom_add_user_code_column');
function custom_show_user_code_column($value, $column_name, $user_id)
{
    if ($column_name === 'user_code') {
        return esc_html(get_user_meta($user_id, 'user_code', true));
    }
    return $value;
}
add_filter('manage_users_custom_column', 'custom_show_user_code_column', 10, 3);

// Hiển thị Code trong trang hồ sơ nhưng không cho chỉnh sửa
function custom_user_profile_code_field($user)
{
    $code = get_user_meta($user->ID, 'user_code', true);
?>
    <h3>Thông tin Code</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_code">Code</label></th>
            <td>
                <p><strong><?php echo esc_html($code); ?></strong></p>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'custom_user_profile_code_field');
add_action('edit_user_profile', 'custom_user_profile_code_field');


// Kiểm tra và tạo Code khi chỉnh sửa hồ sơ người dùng
function custom_generate_code_on_profile_update($user_id)
{
    $code = get_user_meta($user_id, 'user_code', true);

    if (empty($code)) {
        $new_code = 'MS-' . $user_id;
        update_user_meta($user_id, 'user_code', $new_code);
    }
}
add_action('profile_update', 'custom_generate_code_on_profile_update');

// Thêm trường user_address vào trang chỉnh sửa người dùng
function add_user_address_field($user)
{
?>
    <h3>Thông tin địa chỉ</h3>

    <table class="form-table">
        <tr>
            <th><label for="user_address">Địa chỉ</label></th>
            <td>
                <input type="text" name="user_address" id="user_address" value="<?php echo esc_attr(get_user_meta($user->ID, 'user_address', true)); ?>" class="regular-text" /><br />
                <span class="description">Nhập địa chỉ của người dùng.</span>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'add_user_address_field');
add_action('edit_user_profile', 'add_user_address_field');

// Lưu giá trị trường user_address
function save_user_address_field($user_id)
{
    // Kiểm tra quyền truy cập
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Lưu giá trị của user_address
    if (isset($_POST['user_address'])) {
        update_user_meta($user_id, 'user_address', sanitize_text_field($_POST['user_address']));
    }
}
add_action('personal_options_update', 'save_user_address_field');
add_action('edit_user_profile_update', 'save_user_address_field');

// Hiển thị code trong màn list
function custom_add_user_address_column($columns)
{
    $columns['user_address'] = 'Address';
    return $columns;
}
add_filter('manage_users_columns', 'custom_add_user_address_column');
function custom_show_user_address_column($value, $column_name, $user_id)
{
    if ($column_name === 'user_address') {
        return esc_html(get_user_meta($user_id, 'user_address', true));
    }
    return $value;
}
add_filter('manage_users_custom_column', 'custom_show_user_address_column', 10, 3);

// Hiển thị địa chỉ người dùng trên frontend
function display_user_address()
{
    $user_id = get_current_user_id();
    $user_address = get_user_meta($user_id, 'user_address', true);
    return esc_html($user_address);
}

// Thêm trường user_phone vào trang chỉnh sửa người dùng
function add_user_phone_field($user)
{
?>
    <h3>Phone</h3>

    <table class="form-table">
        <tr>
            <th><label for="user_phone">Số điện thoại</label></th>
            <td>
                <input type="text" name="user_phone" id="user_phone" value="<?php echo esc_attr(get_user_meta($user->ID, 'user_phone', true)); ?>" class="regular-text" /><br />
                <span class="description">Nhập Số điện thoại của người dùng.</span>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'add_user_phone_field');
add_action('edit_user_profile', 'add_user_phone_field');

// Lưu giá trị trường user_phone
function save_user_phone_field($user_id)
{
    // Kiểm tra quyền truy cập
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Lưu giá trị của user_phone
    if (isset($_POST['user_phone'])) {
        update_user_meta($user_id, 'user_phone', sanitize_text_field($_POST['user_phone']));
    }
}
add_action('personal_options_update', 'save_user_phone_field');
add_action('edit_user_profile_update', 'save_user_phone_field');

// Hiển thị code trong màn list
function custom_add_user_phone_column($columns)
{
    $columns['user_phone'] = 'Số điện thoại';
    return $columns;
}
add_filter('manage_users_columns', 'custom_add_user_phone_column');
function custom_show_user_phone_column($value, $column_name, $user_id)
{
    if ($column_name === 'user_phone') {
        return esc_html(get_user_meta($user_id, 'user_phone', true));
    }
    return $value;
}
add_filter('manage_users_custom_column', 'custom_show_user_phone_column', 10, 3);

function display_user_phone()
{
    $user_id = get_current_user_id();
    $user_address = get_user_meta($user_id, 'user_phone', true);
    return esc_html($user_address);
}
