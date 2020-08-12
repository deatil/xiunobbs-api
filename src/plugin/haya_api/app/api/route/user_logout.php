<?php

/**
 *      Name: 退出
 *       Url: haya_api-user_logout.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str, token}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

$token = haya_api_param('token');
if (empty($token)) {
    haya_api_return_message(404, 'token不能为空');
}

if (strlen($token) != 32) {
    haya_api_return_message(404, 'token错误');
}

$user_session = haya_api_session_read_by_token($token);
if (empty($user_session)
    || $user_session['logout'] != 0
) {
    haya_api_return_message(404, '登录信息错误');
}

haya_api_session__update($user_session['id'], array(
    'logout' => 1,
    'logout_date' => $time,
    'logout_ip' => $longip,
));

// 记录日志
haya_api_user_log_create(array(
    'app_id' => $app_id,
    'uid' => $user_session['uid'],
    'token' => $token,
    'type' => 'logout',
));

haya_api_return_message(200, '请求成功');

?>