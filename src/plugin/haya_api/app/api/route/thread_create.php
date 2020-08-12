<?php

/**
 *      Name: 发表帖子
 *       Url: haya_api-thread_create.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str, fid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户发表帖子
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

$fid = haya_api_param('fid', 0);
$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(500, lang('forum_not_exists'));

$r = forum_access_user($fid, $gid, 'allowthread');
!$r AND message(-1, lang('user_group_insufficient_privilege'));

$subject = haya_api_param('subject');
empty($subject) AND haya_api_return_message(500, lang('please_input_subject'));
xn_strlen($subject) > 128 AND haya_api_return_message(500, lang('subject_length_over_limit', array('maxlength'=>128)));

$message = haya_api_param('message', '', FALSE);
empty($message) AND haya_api_return_message(500, lang('please_input_message'));
$doctype = haya_api_param('doctype', 0);
$doctype > 10 AND haya_api_return_message(500, lang('doc_type_not_supported'));
xn_strlen($message) > 2028000 AND haya_api_return_message(500, lang('message_too_long'));

$thread = array (
    'fid'=>$fid,
    'uid'=>$uid,
    'sid'=>$sid,
    'subject'=>$subject,
    'message'=>$message,
    'time'=>$time,
    'longip'=>$longip,
    'doctype'=>$doctype,
);

// hook thread_create_thread_before.php

$tid = thread_create($thread, $pid);
$pid === FALSE AND haya_api_return_message(500, lang('create_post_failed'));
$tid === FALSE AND haya_api_return_message(500, lang('create_thread_failed'));

haya_api_return_message(200, '请求成功');

?>