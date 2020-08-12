<?php

/**
 *
 *      Name: 回复详情
 *       Url: haya_api-post_info.htm
 *    Method: GET
 *      Body: {app_id, sign, nonce_str, pid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '请求错误');
}

$pid = haya_api_param('pid');

if (empty($pid)) {
    haya_api_return_message(404, '回复ID错误');
}

$post = post_read($pid);
if (empty($post)) {
    haya_api_return_message(404, '回复信息不存在');
}

$post['user'] = haya_api_user_single_read($post['uid']);

$attachlist = $imagelist = $filelist = array();
if($post['files']) {
    list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid);
}

$post['attachlist'] = $attachlist;
$post['imagelist'] = $imagelist;
$post['filelist'] = $filelist;

haya_api_return_message(200, '获取成功', $post);

?>