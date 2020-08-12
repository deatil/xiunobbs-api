<?php

/**
 *         Name: 创建/注册 用户
 *          Url: haya_api-user_create.htm
 *       Method: POST
 *         Body: {app_id, sign, nonce_str, email, username, password}
 *     Response: {code: 200, msg: '请求成功', data: {}}
 *       Author: deatil
 *     Keywords: 创建用户,注册
 *  Description: 用户注册相关
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_api_route_user_create_start.php

$email = haya_api_param('email');
$username = haya_api_param('username');
$password = haya_api_param('password');

if (empty($email)) {
    haya_api_return_message(404, '邮箱不能为空');
}
if (empty($username)) {
    haya_api_return_message(404, '用户名不能为空');
}
if (empty($password)) {
    haya_api_return_message(404, '密码不能为空');
}

// hook plugin_haya_api_api_route_user_check_before.php

!is_email($email, $err) AND haya_api_return_message(404, $err);
$_user = user_read_by_email($email);
$_user AND haya_api_return_message(404, '邮箱已经被占用');

!is_username($username, $err) AND haya_api_return_message(404, $err);
$_user = user_read_by_username($username);
$_user AND haya_api_return_message(404, '用户名已经被占用');

!is_password($password, $err) AND haya_api_return_message(404, $err);

// hook plugin_haya_api_api_route_user_check_after.php

$salt = xn_rand(16);
$pwd = md5($password.$salt);
$gid = 101;
$_user = array (
    'username' => $username,
    'email' => $email,
    'password' => $pwd,
    'salt' => $salt,
    'gid' => $gid,
    'create_ip' => $longip,
    'create_date' => $time,
    'logins' => 1,
    'login_date' => $time,
    'login_ip' => $longip,
);
$uid = user_create($_user);
$uid === FALSE AND haya_api_return_message('email', '账号注册失败');
$user = user_read($uid);

// hook plugin_haya_api_api_route_user_end.php

haya_api_return_message(200, '请求成功');

?>