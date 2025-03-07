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

    // Nếu chưa có Code, tự động tạo
    if (empty($code)) {
        $new_code = 'MS-' . $user_id;
        update_user_meta($user_id, 'user_code', $new_code);
    }
}
add_action('profile_update', 'custom_generate_code_on_profile_update');
