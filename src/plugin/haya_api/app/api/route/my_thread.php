<?php

/**
 *      Name: 我的论坛帖子
 *       Url: haya_api-my_thread.htm
 *    Method: GET
 *      Body: {page,pagesize}
 *  Response: {code: 200, msg: '获取成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

// hook plugin_haya_api_my_thread_start.php

// hook plugin_haya_api_my_thread_login_check_before.php
haya_api_user_login_check();

$page = haya_api_param('page', 1);
$pagesize = haya_api_param('pagesize', 20);
$pagesize = max(30, $pagesize);
$totalnum = $user['threads'];

// hook plugin_haya_api_my_thread_list_before.php

$threadlist = mythread_find_by_uid($uid, $page, $pagesize);

// hook plugin_haya_api_my_thread_list_after.php

$data = array(
    'page' => $page,
    'pagesize' => $pagesize,
    'total' => $totalnum,
    'list' => $threadlist,
);

// hook plugin_haya_api_my_thread_end.php

haya_api_return_message(200, '获取成功', $data);

?>