<?php

/**
 *      Name: 编辑帖子
 *       Url: haya_api-thread_update.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str, fid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户编辑自己发布的帖子
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_thread_update_start.php

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

// hook plugin_haya_api_thread_update_login_check_before.php
haya_api_user_login_check();

$tid = haya_api_param('tid');
if (empty($tid)) {
    haya_api_return_message(404, '帖子ID不能为空');
}

// hook plugin_haya_api_thread_update_thread_read_before.php
$thread = thread_read($tid);
empty($thread) AND haya_api_return_message(200, lang('thread_not_exists'));

// hook plugin_haya_api_thread_update_post_read_before.php
$post = haya_api_post_read_by_tid_and_isfirst($tid, 1);
empty($post) AND haya_api_return_message(200, lang('post_not_exists'));

$fid = $thread['fid'];
// hook plugin_haya_api_thread_update_forum_read_before.php
$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(200, lang('forum_not_exists'));

// hook plugin_haya_api_thread_update_access_check_before.php
!forum_access_user($fid, $gid, 'allowpost') AND haya_api_return_message(200, lang('user_group_insufficient_privilege'));
$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
!$allowupdate AND !$post['allowupdate'] AND haya_api_return_message(200, lang('have_no_privilege_to_update'));
!$allowupdate AND $thread['closed'] AND haya_api_return_message(200, lang('thread_has_already_closed'));

$subject = htmlspecialchars(haya_api_param('subject', '', FALSE));
$message = haya_api_param('message', '', FALSE);
$doctype = haya_api_param('doctype', 0);

// hook plugin_haya_api_thread_update_post_start.php

empty($message) AND haya_api_return_message(200, lang('please_input_message'));
mb_strlen($message, 'UTF-8') > 2048000 AND haya_api_return_message(200, lang('message_too_long'));

$arr = array();

$newfid = haya_api_param('fid');
$forum = forum_read($newfid);
empty($forum) AND haya_api_return_message(200, lang('forum_not_exists'));

if($fid != $newfid) {
    !forum_access_user($fid, $gid, 'allowthread') AND haya_api_return_message(200, lang('user_group_insufficient_privilege'));
    $post['uid'] != $uid AND !forum_access_mod($fid, $gid, 'allowupdate') AND haya_api_return_message(200, lang('user_group_insufficient_privilege'));
    $arr['fid'] = $newfid;
}
if($subject != $thread['subject']) {
    mb_strlen($subject, 'UTF-8') > 80 AND haya_api_return_message(200, lang('subject_max_length', array('max'=>80)));
    $arr['subject'] = $subject;
}
// hook plugin_haya_api_thread_update_thread_update_before.php
$arr AND thread_update($tid, $arr) === FALSE AND haya_api_return_message(200, lang('update_thread_failed'));

$r = post_update($pid, array('doctype'=>$doctype, 'message'=>$message));
$r === FALSE AND haya_api_return_message(200, lang('update_post_failed'));

// hook plugin_haya_api_thread_update_end.php

haya_api_return_message(200, '请求成功');

?>