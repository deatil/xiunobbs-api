<?php

/**
 *      Name: 板块信息
 *       Url: haya_api-forum_info.htm
 *    Method: GET
 *      Body: {app_id, sign, nonce_str, fid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 板块详情信息
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '访问错误');
}

$fid = haya_api_param('fid');
if (empty($fid)) {
    haya_api_return_message(404, '板块ID不能为空');
}

$forum = forum_read($fid);
if (empty($forum)) {
    haya_api_return_message(404, lang('forum_not_exists'));
}

if (!forum_access_user($fid, $gid, 'allowread')) {
    haya_api_return_message(500, lang('insufficient_visit_forum_privilege'));
}

haya_api_return_message(200, '请求成功', $forum);

?>