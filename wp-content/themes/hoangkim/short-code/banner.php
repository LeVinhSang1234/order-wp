<?php
function mytheme_banner_shortcode()
{
    $phone = get_option('custom_phone');
    if ($phone) {
        return '<p>Hotline: <a href="tel:' . esc_attr(str_replace(' ', '', $phone)) . '">' . esc_html($phone) . '</a></p>';
    }
    return '';
}
add_shortcode('banner', 'mytheme_banner_shortcode');
