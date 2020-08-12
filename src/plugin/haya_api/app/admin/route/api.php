<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '接口列表 - API接口';

$action2 = param(2);
empty($action2) and $action2 = '';

if ($action2 == 'detail') {
    if ($method != 'GET') {
        message(-1, jump('访问错误'));
    }
    
    $api_id = param(3, '');
    if (empty($api_id)) {
        message(-1, jump('接口不存在'));
    }
    
    $api_file = APP_PATH.'plugin/haya_api/app/api/route/'.base64_decode($api_id);
    $haya_api_api = haya_api_apis_read($api_file);
    if (empty($haya_api_api)) {
        message(-1, jump('接口不存在'));
    }
    
    include _include($haya_api_admin_view.'/api_detail.htm');

} else {
    if ($method != 'GET') {
        message(-1, jump('访问错误'));
    }
    
    // hook haya_api_admin_api_start.php

    $haya_api_apis = kv_get('haya_api_apis');
    if (empty($haya_api_apis)) {
        $api_dir = APP_PATH.'plugin/haya_api/app/api/route/';
        $haya_api_apis = haya_api_apis_find_by_dir($api_dir);
        
        kv_set('haya_api_apis', $haya_api_apis);
    }
    
    $haya_api_apis_count = count($haya_api_apis);
    
    // hook haya_api_admin_api_end.php

    include _include($haya_api_admin_view.'/api_index.htm');
}

?>