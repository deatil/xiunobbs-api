<?php

/**
 *      Name: 编辑回帖
 *       Url: haya_api-post_update.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户编辑自己的回帖
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_post_update_start.php

$pid = haya_api_param('pid');
$post = post_read($pid);
empty($post) AND haya_api_return_message(500, lang('post_not_exists'));

$tid = $post['tid'];
$thread = thread_read($tid);
empty($thread) AND haya_api_return_message(500, lang('thread_not_exists'));

$fid = $thread['fid'];
$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(-1, lang('forum_not_exists'));

!forum_access_user($fid, $gid, 'allowpost') AND haya_api_return_message(500, lang('user_group_insufficient_privilege'));
$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
!$allowupdate AND !$post['allowupdate'] AND haya_api_return_message(500, lang('have_no_privilege_to_update'));
!$allowupdate AND $thread['closed'] AND haya_api_return_message(500, lang('thread_has_already_closed'));

$message = haya_api_param('message', '', FALSE);
$doctype = haya_api_param('doctype', 0);

empty($message) AND haya_api_return_message(500, lang('please_input_message'));
mb_strlen($message, 'UTF-8') > 2048000 AND haya_api_return_message('message', lang('message_too_long'));

$r = post_update($pid, array('doctype'=>$doctype, 'message'=>$message));
$r === FALSE AND haya_api_return_message(500, lang('update_post_failed'));

// hook plugin_haya_api_post_update_end.php

haya_api_return_message(200, '请求成功');

?>