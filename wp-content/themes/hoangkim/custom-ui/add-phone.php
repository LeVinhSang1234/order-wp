<?php
// Thêm trường "Phone Number" vào Settings → General
function mytheme_add_phone_field()
{
    add_settings_field(
        'custom_phone',
        __('Phone Number', 'mytheme'),
        'mytheme_phone_field_callback',
        'general',
        'default'
    );

    register_setting('general', 'custom_phone', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ));
}
add_action('admin_init', 'mytheme_add_phone_field');

// Hiển thị ô nhập số điện thoại trong Settings → General
function mytheme_phone_field_callback()
{
    $phone = get_option('custom_phone');
?>
    <input type="text" id="custom_phone" name="custom_phone" value="<?php echo esc_attr($phone); ?>" style="width: 40%;" placeholder="Nhập số điện thoại..." />
<?php
}

// Hiển thị số điện thoại trên website
function mytheme_display_phone_number()
{
    $phone = get_option('custom_phone');
    if ($phone) {
        echo '<p>Hotline: <a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></p>';
    }
}
