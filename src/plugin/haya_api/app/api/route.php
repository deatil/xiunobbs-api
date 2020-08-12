<?php

/**
 * API对外接口
 * 
 * @create 2018-11-12
 * @author deatil
 * 
 * @license GPL2 
**/
 
defined('DEBUG') OR exit('Forbidden');

$action = param(1);
if (empty($action)) {
    haya_api_return_message(404, '访问错误');
}

// hook plugin_haya_api_api_route_start.php

switch (strtolower($action)) {
    // hook plugin_haya_api_api_route_case_start.php
    case 'error':
        // hook plugin_haya_api_api_route_case_error_start.php
        $action_file = APP_PATH.'plugin/haya_api/app/api/route/error.php';
        // hook plugin_haya_api_api_route_case_error_end.php
    default:
        // hook plugin_haya_api_api_route_case_default_start.php
        $action_file = APP_PATH.'plugin/haya_api/app/api/route/'.$action.'.php';
        // hook plugin_haya_api_api_route_case_default_end.php
    // hook plugin_haya_api_api_route_case_end.php
}

// hook plugin_haya_api_api_route_error_before.php

if (!file_exists($action_file)) {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_api_route_end.php

include _include($action_file);

