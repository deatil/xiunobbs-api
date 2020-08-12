<?php

/**
 *         Name: 登录
 *          Url: haya_api-user_login.htm
 *       Method: POST
 *         Body: {app_id, sign, nonce_str, email, password}
 *     Response: {code: 200, msg: '请求成功', data: {}}
 *       Author: deatil
 *     Keywords: 登录
 *  Description: 用户登录API接口
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

$email = haya_api_param('email');
$password = haya_api_param('password');
if (empty($email)) {
    haya_api_return_message(404, '邮箱不能为空');
}

if (is_email($email, $err)) {
    $_user = user_read_by_email($email);
    if (empty($_user)) {
        haya_api_return_message(404, '邮箱不存在');
    }
} else {
    $_user = user_read_by_username($email);
    if (empty($_user)) {
        haya_api_return_message(404, '用户名不存在');
    }
}

$uid = $_user['uid'];

if (!is_password($password, $err)) {
    haya_api_return_message(404, $err);
}
if (md5($password.$_user['salt']) != $_user['password']) {
    haya_api_return_message(404, "密码错误");
}

$user_session = haya_api_session_read_by_app_id_and_uid($app_id, $uid);
if (empty($user_session)) {
    $user_token = md5(user_token_gen($_user['uid']));
    $user_session_status = haya_api_session_create(array(
        'app_id' => $app_id,
        'uid' => $_user['uid'],
        'token' => $user_token,
        'logout' => 0,
        'logins' => 1,
    ));

    if ($user_session_status === false) {
        haya_api_return_message(404, "登录失败");
    }
} else {
    $user_token = md5(user_token_gen($_user['uid']));
    
    // 更新当前session及授权次数
    haya_api_session__update($user_session['id'], array(
        'token' => $user_token,
        'logout' => 0,
        'logins+' => 1
    ));
}

// 更新登录时间和次数
user_update($_user['uid'], array(
    'login_ip' => $longip, 
    'login_date' => $time, 
    'logins+' => 1
));

// 记录日志
haya_api_user_log_create(array(
    'app_id' => $app_id,
    'uid' => $_user['uid'],
    'token' => $user_token,
    'type' => 'login',
));

$data = array(
    'token' => $user_token,
);

haya_api_return_message(200, '请求成功', $data);

?>