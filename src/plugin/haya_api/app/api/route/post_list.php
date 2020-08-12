<?php

/**
 *      Name: 回复列表
 *       Url: haya_api-post_list.htm
 *    Method: GET
 *      Body: {app_id, sign, nonce_str[, start][, limit][, tid][, uid][, ordertype][, order]}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '请求错误');
}

$tid = haya_api_param('tid', 0);
$page = haya_api_param('page', 1);
$pagesize = haya_api_param('pagesize', 15);
$pagesize = ($conf['postlist_pagesize'] < $pagesize) ? $conf['postlist_pagesize'] : $pagesize;

// hook plugin_haya_api_post_list_start.php

$thread = thread_read($tid);
if (empty($thread)) {
    haya_api_return_message(404, lang('thread_not_exists'));
}

$total = post_count(array(
    'tid' => $tid,
));

$postlist = post_find_by_tid($tid, $page, $pagesize);
if (empty($postlist)) {
    haya_api_return_message(404, lang('post_not_exists'));
}

$attachlist = $imagelist = $filelist = array();

thread_inc_views($tid);

$fid = $thread['fid'];

$allowpost = forum_access_user($fid, $gid, 'allowpost') ? 1 : 0;
$allowupdate = forum_access_mod($fid, $gid, 'allowupdate') ? 1 : 0;
$allowdelete = forum_access_mod($fid, $gid, 'allowdelete') ? 1 : 0;

forum_access_user($fid, $gid, 'allowread') OR haya_api_return_message(302, lang('user_group_insufficient_privilege'));

$data = array(
    'page' => $page,
    'pagesize' => $pagesize,
    'total' => $total,
    'list' => $postlist,
    'access' => array(
        'allowpost' => $allowpost,
        'allowupdate' => $allowupdate,
        'allowdelete' => $allowdelete,
    ),
);

// hook plugin_haya_api_post_list_end.php

haya_api_return_message(200, '获取成功', $data);

?>