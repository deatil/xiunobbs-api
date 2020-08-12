<?php

/**
 *      Name: 删除帖子
 *       Url: haya_api-thread_delete.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str, fid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户删除用户自己发布的帖子
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_thread_delete_login_check_before.php
haya_api_user_login_check();

$tid = haya_api_param('tid', 0);
if (empty($tid)) {
    haya_api_return_message(404, '帖子ID不能为空');
}

// hook plugin_haya_api_thread_delete_thread_read_before.php

$thread = thread_read($tid);
empty($thread) AND haya_api_return_message(400, lang('thread_not_exists'));

$fid = $thread['fid'];

// hook plugin_haya_api_thread_delete_forum_read_before.php

$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(400, lang('forum_not_exists'));

// hook plugin_haya_api_thread_delete_check_before.php

!forum_access_user($fid, $gid, 'allowpost') AND haya_api_return_message(400, lang('user_group_insufficient_privilege'));
$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
!$allowdelete AND $thread['closed'] AND haya_api_return_message(400, lang('thread_has_already_closed'));

// hook plugin_haya_api_thread_delete_middle.php

thread_delete($tid);

// hook plugin_haya_api_thread_delete_end.php

haya_api_return_message(200, '请求成功');

?>