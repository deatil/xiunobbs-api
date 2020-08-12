<?php

/**
 *      Name: 用户的论坛帖子
 *       Url: haya_api-user_thread.htm
 *    Method: GET
 *      Body: {}
 *  Response: {code: 200, msg: '获取成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_user_thread_start.php

$_uid = haya_api_param('uid');
if (empty($_uid)) {
    haya_api_return_message(404, 'UID不能为空');
}

$_user = user_read($_uid);

empty($_user) AND haya_api_return_message(404, lang('user_not_exists'));

$page = haya_api_param('page', 1);
$pagesize = haya_api_param('pagesize', 20);
$pagesize = max(30, $pagesize);

$totalnum = $_user['threads'];
$threadlist = mythread_find_by_uid($_uid, $page, $pagesize);
thread_list_access_filter($threadlist, $gid);

$data = array(
    'page' => $page,
    'pagesize' => $pagesize,
    'total' => $totalnum,
    'list' => $threadlist,
);

// hook plugin_haya_api_user_thread_end.php

haya_api_return_message(200, '获取成功', $data);

?>