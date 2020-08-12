<?php

/**
 *      Name: 更新我的头像
 *       Url: haya_api-my_avatar.htm
 *    Method: POST
 *      Body: {width,height,data}
 *  Response: {code: 200, msg: '获取成功', data: {avatar}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_my_avatar_start.php

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_my_avatar_login_check_before.php
haya_api_user_login_check();

$width = haya_api_param('width');
$height = haya_api_param('height');
$data = haya_api_param('data', '');

empty($data) AND haya_api_return_message(500, lang('data_is_empty'));
$data = base64_decode_file_data($data);
$size = strlen($data);
$size > 40000 AND haya_api_return_message(500, lang('filesize_too_large', array(
    'maxsize' => '40K', 
    'size' => $size,
)));

$filename = "$uid.png";
$dir = substr(sprintf("%09d", $uid), 0, 3).'/';
$path = $conf['upload_path'].'avatar/'.$dir;
$url = $conf['upload_url'].'avatar/'.$dir.$filename;
!is_dir($path) AND (mkdir($path, 0777, TRUE) OR haya_api_return_message(502, lang('directory_create_failed')));

// hook plugin_haya_api_my_avatar_post_save_before.php
file_put_contents($path.$filename, $data) OR haya_api_return_message(500, lang('write_to_file_failed'));

user_update($uid, array('avatar' => $time));

$data = array(
    'avatar' => $url,
);

// hook plugin_haya_api_my_avatar_end.php

haya_api_return_message(200, '获取成功', $data);

?>