<?php

/**
 *      Name: 用户信息
 *       Url: haya_api-user_info.htm
 *    Method: GET
 *      Body: {app_id, sign, nonce_str, uid[, token]}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 获取用户详情信息
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '访问错误');
}

$haya_api_uid = haya_api_param('uid');
if (empty($haya_api_uid)) {
    haya_api_return_message(404, 'UID不能为空');
}

// hook plugin_haya_api_api_route_user_start.php

$haya_api_token = haya_api_param('token');
$haya_api_token = trim($haya_api_token);
$haya_api_session = haya_api_session_read_by_token($haya_api_token);
if (!empty($haya_api_session)
    && $haya_api_session['uid'] == $haya_api_uid
) {
    $unsets = array();
    // hook plugin_haya_api_api_route_user_session_after.php
} else {
    $unsets = array('email');
    // hook plugin_haya_api_api_route_user_no_session_after.php
}
 
$data = haya_api_user_read($haya_api_uid, $unsets);
if (empty($data)) {
    haya_api_return_message(404, '用户不存在');
}

// hook plugin_haya_api_api_route_user_end.php

haya_api_return_message(200, '请求成功', $data);

?>