<?php
function custom_api_register_routes()
{
    register_rest_route('/v1', '/exchange-rate', array(
        'methods'  => 'GET',
        'callback' => 'custom_api_hello_world',
        'permission_callback' => '__return_true',
    ));
}

function custom_api_hello_world()
{
    $exchange_rate = get_option('exchange_rate', 1.0);
    return wp_send_json_success($exchange_rate);
}

add_action('rest_api_init', 'custom_api_register_routes');

function custom_api_add_cors_headers($value, $server, $request)
{
    $route = $request->get_route();
    if ($route === '/v1/exchange-rate/') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
    }

    return $value;
}

add_action('rest_api_init', 'custom_api_register_routes');
add_filter('rest_pre_serve_request', 'custom_api_add_cors_headers', 10, 3);
