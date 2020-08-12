<?php

/**
 *      Name: 删除回帖
 *       Url: haya_api-post_delete.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str,pid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户删除自己的回帖
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

$pid = haya_api_param('pid', 0);

// hook plugin_haya_api_post_delete_start.php

if ($method != 'POST') haya_api_return_message(500, lang('method_error'));

$post = post_read($pid);
empty($post) AND haya_api_return_message(500, lang('post_not_exists'));

$tid = $post['tid'];
$thread = thread_read($tid);
empty($thread) AND haya_api_return_message(500, lang('thread_not_exists'));

$fid = $thread['fid'];
$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(500, lang('forum_not_exists'));

!forum_access_user($fid, $gid, 'allowpost') AND haya_api_return_message(500, lang('user_group_insufficient_privilege'));
$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
!$allowdelete AND !$post['allowdelete'] AND haya_api_return_message(500, lang('insufficient_delete_privilege'));
!$allowdelete AND $thread['closed'] AND haya_api_return_message(500, lang('thread_has_already_closed'));

// hook plugin_haya_api_post_delete_middle.php

post_delete($pid);

// hook plugin_haya_api_post_delete_end.php

haya_api_return_message(200, '请求成功');

?>