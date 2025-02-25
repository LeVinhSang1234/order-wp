<?php
// Thêm trường Logo vào "Cài đặt Chung" trước Site Icon
function mytheme_add_logo_field()
{
    add_settings_field(
        'custom_logo',
        __('Website Logo', 'mytheme'),
        'mytheme_logo_field_callback',
        'general',
        'default'
    );

    register_setting('general', 'custom_logo', array(
        'type'              => 'string',
        'sanitize_callback' => 'esc_url',
        'default'           => '',
    ));
}
add_action('admin_init', 'mytheme_add_logo_field');

// Hiển thị trường upload logo trong Cài đặt Chung
function mytheme_logo_field_callback()
{
    $logo_url = get_option('custom_logo');
?>
    <input type="text" id="custom_logo" name="custom_logo" value="<?php echo esc_attr($logo_url); ?>" style="width: 60%;" />
    <button type="button" class="button upload_logo_button">Upload Logo</button>
    <button type="button" class="button remove_logo_button">Xóa Logo</button>

    <p><img id="logo_preview" src="<?php echo esc_url($logo_url); ?>" style="max-width: 200px; margin-top: 10px; <?php echo $logo_url ? '' : 'display:none;'; ?>"></p>
<?php
}

// Nạp script để mở Media Uploader
function mytheme_admin_scripts($hook)
{
    if ($hook !== 'options-general.php') return;
    wp_enqueue_media();
    wp_enqueue_script('custom-logo-upload', get_template_directory_uri() . '/js/custom-logo-upload.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'mytheme_admin_scripts');

// JavaScript để mở Media Uploader & xử lý ảnh logo
function mytheme_custom_logo_script()
{
?>
    <script>
        jQuery(document).ready(function($) {
            // Mở Media Uploader khi nhấn "Upload Logo"
            $('.upload_logo_button').click(function(e) {
                e.preventDefault();

                var custom_uploader = wp.media({
                    title: 'Chọn hoặc Tải lên Logo',
                    button: {
                        text: 'Chọn Logo'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#custom_logo').val(attachment.url);
                    $('#logo_preview').attr('src', attachment.url).show();
                }).open();
            });

            // Xóa Logo khi nhấn "Xóa Logo"
            $('.remove_logo_button').click(function(e) {
                e.preventDefault();
                $('#custom_logo').val('');
                $('#logo_preview').hide();
            });
        });
    </script>
<?php
}
add_action('admin_footer', 'mytheme_custom_logo_script');

// Hiển thị logo trong header.php
function mytheme_display_custom_logo()
{
    $custom_logo = get_option('custom_logo');
    if ($custom_logo) {
        echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link">';
        echo '<img src="' . esc_url($custom_logo) . '" alt="' . get_bloginfo('name') . '">';
        echo '</a>';
    } else {
        echo '<h1 class="site-title">' . get_bloginfo('name') . '</h1>';
    }
}
