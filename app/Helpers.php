<?php
function get_platform(){
    return Request::header('X-Client-Platform');
}

function is_request_from_api()
{
    return $_SERVER['SERVER_NAME'] == env('API_DOMAIN');
}

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/* 根据路由激活元素 */
function set_active($path, $active = 'active')
{
    return (Request::is($path) || Request::is($path.'*')) ? $active : '';
}

function set_hidden($paths, $hidden = 'hidden')
{
    if(!is_array($paths)) {
        return !Request::is($paths.'*') ? $hidden : '';
    }

    foreach ($paths as $path) {
        if(Request::is($path.'*')) {
            return '';
        }
    }

    return $hidden;
}

function navViewActive($anchor)
{
    return Route::currentRouteName() == $anchor ? 'active' : '';
}

function back_url($route_name)
{
    $back_url = Session::get('back_url');
//    Session::put('back_url', '');
    return empty($back_url) || $back_url == Request::url() ? route($route_name) : $back_url;
}

function except_empty($val)
{
    return $val != null && $val != '';
}

function prev_url($route_name)
{
    $pre_arr = explode('?', URL::previous());
    return $pre_arr[0] == route($route_name) ? URL::previous() : route($route_name);
}