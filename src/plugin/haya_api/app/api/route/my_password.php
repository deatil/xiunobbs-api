<?php

/**
 *      Name: 更改我的密码
 *       Url: haya_api-my_password.htm
 *    Method: POST
 *      Body: {password_old,password_new,password_new_repeat}
 *  Response: {code: 200, msg: '获取成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_my_password_start.php

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_my_password_login_check_before.php
haya_api_user_login_check();
    
// hook plugin_haya_api_my_password_post_start.php

$password_old = haya_api_param('password_old');
$password_new = haya_api_param('password_new');
$password_new_repeat = haya_api_param('password_new_repeat');
$password_new_repeat != $password_new AND haya_api_return_message(500, lang('repeat_password_incorrect'));
md5($password_old.$user['salt']) != $user['password'] AND haya_api_return_message(501, lang('old_password_incorrect'));
$password_new = md5($password_new.$user['salt']);
$r = user_update($uid, array('password' => $password_new));
$r === FALSE AND haya_api_return_message(500, lang('password_modify_failed'));

// hook plugin_haya_api_my_password_post_end.php
haya_api_return_message(200, lang('password_modify_successfully'));

?>