<?php

/**
 *      Name: 创建回帖
 *       Url: haya_api-post_create.htm
 *    Method: POST
 *      Body: {app_id, sign, nonce_str}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *  Description: 登陆用户创建主题的回帖
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'POST') {
    haya_api_return_message(500, '访问错误');
}

$tid = haya_api_param('tid', 0);
$quick = haya_api_param('quick', 0);

$thread = thread_read($tid);
empty($thread) AND haya_api_return_message(500, lang('thread_not_exists'));

$fid = $thread['fid'];

$forum = forum_read($fid);
empty($forum) AND haya_api_return_message(500, lang('forum_not_exists'));

$r = forum_access_user($fid, $gid, 'allowpost');
if(!$r) {
    haya_api_return_message(-1, lang('user_group_insufficient_privilege'));
}

($thread['closed'] && ($gid == 0 || $gid > 5)) AND haya_api_return_message(500, lang('thread_has_already_closed'));

$message = haya_api_param('message', '', FALSE);
empty($message) AND haya_api_return_message(500, lang('please_input_message'));

$doctype = haya_api_param('doctype', 0);
xn_strlen($message) > 2028000 AND haya_api_return_message(500, lang('message_too_long'));

$thread['top'] > 0 AND thread_top_cache_delete();

$quotepid = haya_api_param('quotepid', 0);
$quotepost = post__read($quotepid);
(!$quotepost || $quotepost['tid'] != $tid) AND $quotepid = 0;

$post = array(
    'tid'=>$tid,
    'uid'=>$uid,
    'create_date'=>$time,
    'userip'=>$longip,
    'isfirst'=>0,
    'doctype'=>$doctype,
    'quotepid'=>$quotepid,
    'message'=>$message,
);
$pid = post_create($post, $fid, $gid);
empty($pid) AND haya_api_return_message(500, lang('create_post_failed'));

$data = array(
    'pid' => $pid,
);

haya_api_return_message(200, '请求成功', $data);

?>