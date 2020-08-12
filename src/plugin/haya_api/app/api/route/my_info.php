<?php

/**
 *      Name: 我的信息
 *       Url: haya_api-my_info.htm
 *    Method: GET
 *      Body: {}
 *  Response: {code: 200, msg: '获取成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_my_info_start.php

if ($method != 'GET') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_my_info_login_check_before.php
haya_api_user_login_check();

// hook plugin_haya_api_my_info_unsets_before.php
$unsets = array();

// hook plugin_haya_api_my_info_read_before.php
$data = haya_api_user_read($uid, $unsets);
if (empty($data)) {
    haya_api_return_message(404, '用户不存在');
}

// hook plugin_haya_api_my_info_end.php

haya_api_return_message(200, '获取成功', $data);

?>